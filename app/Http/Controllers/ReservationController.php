<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Accommodation;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations (for authenticated user)
     */
    public function index(Request $request): Response
    {
        try {
            // no auth: return all reservations for demo
            $reservations = Reservation::with(['customer', 'accommodation'])->get();

            return response([
                'success' => true,
                'data' => $reservations,
                'message' => 'Reservations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error retrieving reservations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created reservation
     */
    public function store(Request $request): Response
    {
        try {
            // normalize input keys to accept both camelCase and snake_case from different frontends
            $request->merge([
                'guestName' => $request->input('guestName') ?? $request->input('guest_name') ?? $request->input('name'),
                'guestEmail' => $request->input('guestEmail') ?? $request->input('guest_email') ?? $request->input('email'),
                'guestPhone' => $request->input('guestPhone') ?? $request->input('phone'),
                'accommodationId' => $request->input('accommodationId') ?? $request->input('accommodation_id'),
                'accommodationType' => $request->input('accommodationType') ?? $request->input('accommodation_type'),
                'roomNumber' => $request->input('roomNumber') ?? $request->input('room_number'),
                'checkIn' => $request->input('checkIn') ?? $request->input('check_in') ?? $request->input('check_in_date'),
                'checkOut' => $request->input('checkOut') ?? $request->input('check_out') ?? $request->input('check_out_date'),
                'nights' => $request->input('nights') ?? $request->input('number_of_nights'),
                'status' => $request->input('status') ?? $request->input('reservation_status'),
                'totalAmount' => $request->input('totalAmount') ?? $request->input('total_price'),
            ]);

            // validate front-end fields
            $validated = $request->validate([
                'guestName' => 'required|string',
                'guestEmail' => 'required|email',
                'guestPhone' => 'nullable|string',
                // accept either an accommodation id (preferred) or an accommodationType string
                'accommodationId' => 'nullable',
                'accommodationType' => 'nullable|string',
                // roomNumber is optional in the current UI
                'roomNumber' => 'nullable|string',
                'checkIn' => 'required|date',
                'checkOut' => 'required|date',
                'nights' => 'nullable|integer|min:1',
                'status' => 'nullable|string',
                'totalAmount' => 'nullable|numeric',
            ]);

            // find or create related customer and accommodation to avoid foreign key issues
            $customer = Customer::firstOrCreate(
                ['email' => $validated['guestEmail']],
                ['name' => $validated['guestName'], 'phone' => $validated['guestPhone']]
            );

            // Resolve accommodation: prefer an existing record by id, otherwise try by provided type
            $accommodation = null;
            if (!empty($validated['accommodationId'])) {
                $accommodation = Accommodation::find($validated['accommodationId']);
                if (!$accommodation) {
                    return response([
                        'success' => false,
                        'errors' => ['accommodationId' => ['Accommodation not found']],
                        'message' => 'Validation failed'
                    ], 422);
                }
            }
            if (!$accommodation && !empty($validated['accommodationType'])) {
                $accommodation = Accommodation::firstOrCreate(
                    ['type' => $validated['accommodationType']],
                    [
                        'name' => $validated['accommodationType'],
                        'type' => $validated['accommodationType'],
                        'price_per_night' => $validated['totalAmount'] ? ($validated['totalAmount'] / max(1, ($validated['nights'] ?? 1))) : 0,
                        'available' => true,
                        'capacity' => 1,
                    ]
                );
            }
            // If still not found, create a placeholder accommodation record
            if (!$accommodation) {
                $accommodation = Accommodation::create([
                    'name' => 'Unspecified',
                    'type' => 'room',
                    'price_per_night' => $validated['totalAmount'] ?? 0,
                    'available' => true,
                    'capacity' => 1,
                ]);
            }

            // Calculate nights from dates (server authoritative)
            $checkIn = Carbon::parse($validated['checkIn']);
            $checkOut = Carbon::parse($validated['checkOut']);
            // Set check-in time to the current server time (preserve the chosen date, use now's hours/minutes)
            $now = Carbon::now();
            $checkIn->setTime($now->hour, $now->minute, $now->second);
            $nights = $checkIn->diffInDays($checkOut);
            if ($nights < 1) {
                $nights = 1;
            }

            // Compute total based on accommodation rate when available
            $pricePerNight = $accommodation->price_per_night ?? ($validated['totalAmount'] ? ($validated['totalAmount'] / max(1, ($validated['nights'] ?? 1))) : 0);
            $computedTotal = $nights * $pricePerNight;

            // map to reservation columns; use resolved relational ids
            $data = [
                'customer_id' => $customer->id,
                'accommodation_id' => $accommodation->id,
                'check_in_date' => $checkIn->toDateTimeString(),
                'check_out_date' => $validated['checkOut'],
                'number_of_nights' => $nights,
                'total_price' => $computedTotal,
                'status' => $validated['status'] ?? 'pending',
                'special_requests' => json_encode([
                    'guestName' => $validated['guestName'],
                    'guestEmail' => $validated['guestEmail'],
                    'guestPhone' => $validated['guestPhone'],
                    'accommodationType' => $validated['accommodationType'] ?? null,
                    'accommodationId' => $accommodation->id,
                    'roomNumber' => $validated['roomNumber'] ?? null,
                ]),
            ];

            $reservation = Reservation::create($data);
            $reservation->load(['customer', 'accommodation']);

            return response([
                'success' => true,
                'data' => $reservation,
                'message' => 'Reservation created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error creating reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified reservation
     */
    public function show(Reservation $reservation): Response
    {
        // no authorization checks in demo mode

        return response([
            'success' => true,
            'data' => $reservation->load(['customer', 'accommodation']),
            'message' => 'Reservation retrieved successfully'
        ]);
    }

    /**
     * Update the specified reservation
     */
    public function update(Request $request, Reservation $reservation): Response
    {
        try {
            // no authorization checks in demo mode

            // Only allow updates if not already completed or cancelled
            if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                return response([
                    'success' => false,
                    'message' => 'Cannot update reservation with status: ' . $reservation->status
                ], 409);
            }

            // normalize inputs (accept snake_case or camelCase)
            $request->merge([
                'guestName' => $request->input('guestName') ?? $request->input('guest_name') ?? $request->input('name'),
                'guestEmail' => $request->input('guestEmail') ?? $request->input('guest_email') ?? $request->input('email'),
                'guestPhone' => $request->input('guestPhone') ?? $request->input('phone'),
                'accommodationId' => $request->input('accommodationId') ?? $request->input('accommodation_id'),
                'accommodationType' => $request->input('accommodationType') ?? $request->input('accommodation_type'),
                'roomNumber' => $request->input('roomNumber') ?? $request->input('room_number'),
                'checkIn' => $request->input('checkIn') ?? $request->input('check_in') ?? $request->input('check_in_date'),
                'checkOut' => $request->input('checkOut') ?? $request->input('check_out') ?? $request->input('check_out_date'),
                'nights' => $request->input('nights') ?? $request->input('number_of_nights'),
                'status' => $request->input('status') ?? $request->input('reservation_status'),
                'totalAmount' => $request->input('totalAmount') ?? $request->input('total_price'),
            ]);

            $validated = $request->validate([
                'guestName' => 'string',
                'guestEmail' => 'email',
                'guestPhone' => 'nullable|string',
                'accommodationId' => 'nullable',
                'accommodationType' => 'string',
                'roomNumber' => 'string',
                'checkIn' => 'date',
                'checkOut' => 'date',
                'nights' => 'integer|min:1',
                'status' => 'string',
                'totalAmount' => 'numeric',
            ]);

            // map fields
            $data = [];
            $shouldRecomputeTotal = false;
            if (isset($validated['checkIn'])) {
                $data['check_in_date'] = $validated['checkIn'];
                $shouldRecomputeTotal = true;
            }
            if (isset($validated['checkOut'])) {
                $data['check_out_date'] = $validated['checkOut'];
                $shouldRecomputeTotal = true;
            }
            if (isset($validated['nights'])) {
                $data['number_of_nights'] = $validated['nights'];
                $shouldRecomputeTotal = true;
            }
            if (isset($validated['totalAmount'])) {
                // allow manual override but prefer computed
                $data['total_price'] = $validated['totalAmount'];
            }
            if (isset($validated['status'])) {
                $data['status'] = $validated['status'];
            }
            // store original front-end values in special_requests if provided
            $extras = [];
            foreach (['guestName', 'guestEmail', 'guestPhone', 'accommodationType', 'accommodationId', 'roomNumber'] as $key) {
                if (isset($validated[$key])) {
                    $extras[$key] = $validated[$key];
                }
            }
            if (!empty($extras)) {
                $data['special_requests'] = json_encode($extras);
            }

            // If we should recompute total (dates changed or nights changed or accommodation changed), try to resolve accommodation and recompute
            if ($shouldRecomputeTotal || isset($validated['accommodationId']) || isset($validated['accommodationType'])) {
                $accommodation = null;
                if (!empty($validated['accommodationId'])) {
                    $accommodation = Accommodation::find($validated['accommodationId']);
                    if (!$accommodation) {
                        return response([
                            'success' => false,
                            'errors' => ['accommodationId' => ['Accommodation not found']],
                            'message' => 'Validation failed'
                        ], 422);
                    }
                }
                if (!$accommodation && !empty($validated['accommodationType'])) {
                    $accommodation = Accommodation::where('type', $validated['accommodationType'])->first();
                }

                // determine nights from data: prefer provided check_in/out in $data, else current reservation values
                $checkInVal = $data['check_in_date'] ?? $reservation->check_in_date;
                $checkOutVal = $data['check_out_date'] ?? $reservation->check_out_date;
                try {
                    $ci = Carbon::parse($checkInVal);
                    $co = Carbon::parse($checkOutVal);
                    $n = $ci->diffInDays($co);
                    if ($n < 1) $n = 1;
                    $data['number_of_nights'] = $n;
                } catch (\Exception $e) {
                    // keep existing nights if parse fails
                }

                if ($accommodation) {
                    $ppn = $accommodation->price_per_night ?? 0;
                    $nightsForCalc = $data['number_of_nights'] ?? $reservation->number_of_nights ?? 1;
                    $data['total_price'] = $nightsForCalc * $ppn;
                }
            }

            $reservation->update($data);
            $reservation->load(['customer', 'accommodation']);

            return response([
                'success' => true,
                'data' => $reservation,
                'message' => 'Reservation updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error updating reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified reservation
     */
    public function destroy(Reservation $reservation): Response
    {
        try {
            // no authorization checks in demo mode

            // Only allow deletion if pending or confirmed
            if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                $reservation->update(['status' => 'cancelled']);
                return response([
                    'success' => true,
                    'message' => 'Reservation cancelled successfully'
                ]);
            }

            $reservation->delete();

            return response([
                'success' => true,
                'message' => 'Reservation deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error deleting reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check in a reservation
     */
    public function checkIn(Reservation $reservation): Response
    {
        try {
            $user = Auth::user();
            if ($user instanceof \App\Models\User && !$user->hasRole('admin')) {
                return response([
                    'success' => false,
                    'message' => 'Only admin can check in reservations'
                ], 403);
            }

            if (!$reservation->checkIn()) {
                return response([
                    'success' => false,
                    'message' => 'Reservation must be confirmed before check-in'
                ], 409);
            }

            return response([
                'success' => true,
                'data' => $reservation,
                'message' => 'Checked in successfully'
            ]);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error checking in: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check out a reservation
     */
    public function checkOut(Reservation $reservation): Response
    {
        try {
            $user = Auth::user();
            if ($user instanceof \App\Models\User && !$user->hasRole('admin')) {
                return response([
                    'success' => false,
                    'message' => 'Only admin can check out reservations'
                ], 403);
            }

            if (!$reservation->checkOut()) {
                return response([
                    'success' => false,
                    'message' => 'Reservation must be checked in before check-out'
                ], 409);
            }

            return response([
                'success' => true,
                'data' => $reservation,
                'message' => 'Checked out successfully'
            ]);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error checking out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm a pending reservation
     */
    public function confirm(Reservation $reservation): Response
    {
        try {
            $user = Auth::user();
            if ($user instanceof \App\Models\User && !$user->hasRole('admin')) {
                return response([
                    'success' => false,
                    'message' => 'Only admin can confirm reservations'
                ], 403);
            }

            if ($reservation->status !== 'pending') {
                return response([
                    'success' => false,
                    'message' => 'Only pending reservations can be confirmed'
                ], 409);
            }

            $reservation->update(['status' => 'confirmed']);

            return response([
                'success' => true,
                'data' => $reservation,
                'message' => 'Reservation confirmed successfully'
            ]);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Error confirming reservation: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Accommodation;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
            // validate front-end fields
            $validated = $request->validate([
                'guestName' => 'required|string',
                'guestEmail' => 'required|email',
                'accommodationType' => 'required|string',
                'roomNumber' => 'required|string',
                'checkIn' => 'required|date',
                'checkOut' => 'required|date',
                'nights' => 'nullable|integer|min:1',
                'status' => 'nullable|string',
                'totalAmount' => 'nullable|numeric',
            ]);

            // find or create related customer and accommodation to avoid foreign key issues
            $customer = Customer::firstOrCreate(
                ['email' => $validated['guestEmail']],
                ['name' => $validated['guestName']]
            );

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

            // map to reservation columns; use resolved relational ids
            $data = [
                'customer_id' => $customer->id,
                'accommodation_id' => $accommodation->id,
                'check_in_date' => $validated['checkIn'],
                'check_out_date' => $validated['checkOut'],
                'number_of_nights' => $validated['nights'] ?? 0,
                'total_price' => $validated['totalAmount'] ?? 0,
                'status' => $validated['status'] ?? 'pending',
                'special_requests' => json_encode([
                    'guestName' => $validated['guestName'],
                    'guestEmail' => $validated['guestEmail'],
                    'accommodationType' => $validated['accommodationType'],
                    'roomNumber' => $validated['roomNumber'],
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

            $validated = $request->validate([
                'guestName' => 'string',
                'guestEmail' => 'email',
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
            if (isset($validated['checkIn'])) {
                $data['check_in_date'] = $validated['checkIn'];
            }
            if (isset($validated['checkOut'])) {
                $data['check_out_date'] = $validated['checkOut'];
            }
            if (isset($validated['nights'])) {
                $data['number_of_nights'] = $validated['nights'];
            }
            if (isset($validated['totalAmount'])) {
                $data['total_price'] = $validated['totalAmount'];
            }
            if (isset($validated['status'])) {
                $data['status'] = $validated['status'];
            }
            // store original front-end values in special_requests if provided
            $extras = [];
            foreach (['guestName', 'guestEmail', 'accommodationType', 'roomNumber'] as $key) {
                if (isset($validated[$key])) {
                    $extras[$key] = $validated[$key];
                }
            }
            if (!empty($extras)) {
                $data['special_requests'] = json_encode($extras);
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

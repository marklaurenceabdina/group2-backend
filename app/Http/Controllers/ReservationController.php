<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Accommodation;
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
            $user = Auth::user();

            // Check if user is admin/staff (can view all) or customer (can view only theirs)
            if ($user instanceof \App\Models\User && $user->hasRole('admin')) {
                $reservations = Reservation::with(['customer', 'accommodation'])->get();
            } else {
                $reservations = Reservation::where('customer_id', $user->id)
                    ->with(['accommodation'])
                    ->get();
            }

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
            $validated = $request->validate([
                'customer_id' => 'required|exists:users,id',
                'accommodation_id' => 'required|exists:accommodations,id',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'special_requests' => 'nullable|string',
            ]);

            $user = Auth::user();
            // Verify customer is the authenticated user or admin
            if ($user instanceof \App\Models\User && !$user->hasRole('admin') && Auth::id() !== $validated['customer_id']) {
                return response([
                    'success' => false,
                    'message' => 'Unauthorized: You can only create reservations for yourself'
                ], 403);
            }

            // Check accommodation availability
            $accommodation = Accommodation::findOrFail($validated['accommodation_id']);

            if (!$accommodation->isAvailableForDates($validated['check_in_date'], $validated['check_out_date'])) {
                return response([
                    'success' => false,
                    'message' => 'Accommodation is not available for the selected dates'
                ], 409);
            }

            // Create reservation (numbers will be calculated in model boot)
            $reservation = Reservation::create($validated);
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
        $user = Auth::user();
        // Verify authorization
        if ($user instanceof \App\Models\User && !$user->hasRole('admin') && Auth::id() !== $reservation->customer_id) {
            return response([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

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
            $user = Auth::user();
            // Verify authorization
            if ($user instanceof \App\Models\User && !$user->hasRole('admin') && Auth::id() !== $reservation->customer_id) {
                return response([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Only allow updates if not already completed or cancelled
            if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                return response([
                    'success' => false,
                    'message' => 'Cannot update reservation with status: ' . $reservation->status
                ], 409);
            }

            $validated = $request->validate([
                'check_in_date' => 'date|after_or_equal:today',
                'check_out_date' => 'date',
                'special_requests' => 'nullable|string',
                'status' => 'string|in:pending,confirmed,checked_in,completed,cancelled',
            ]);

            // If dates are being updated, check availability again
            if (isset($validated['check_in_date']) || isset($validated['check_out_date'])) {
                $checkInDate = $validated['check_in_date'] ?? $reservation->check_in_date;
                $checkOutDate = $validated['check_out_date'] ?? $reservation->check_out_date;

                if (!$reservation->accommodation->isAvailableForDates($checkInDate, $checkOutDate)) {
                    return response([
                        'success' => false,
                        'message' => 'Accommodation is not available for the selected dates'
                    ], 409);
                }
            }

            $reservation->update($validated);
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
            $user = Auth::user();
            // Verify authorization
            if ($user instanceof \App\Models\User && !$user->hasRole('admin') && Auth::id() !== $reservation->customer_id) {
                return response([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

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

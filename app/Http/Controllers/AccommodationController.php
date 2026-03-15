<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccommodationController extends Controller
{
    /**
     * Display a listing of accommodations
     */
    public function index(): Response
    {
        $accommodations = Accommodation::all();
        return response([
            'success' => true,
            'data' => $accommodations,
            'message' => 'Accommodations retrieved successfully'
        ]);
    }

    /**
     * Store a newly created accommodation
     */
    public function store(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                // allow additional types used in frontend
                'type' => 'required|string|in:standard,deluxe,suite,villa,room,bungalow',
                'capacity' => 'required|integer|min:1',
                // frontend sends pricePerNight
                'price_per_night' => 'sometimes|numeric|min:0',
                'pricePerNight' => 'sometimes|numeric|min:0',
                // frontend sends status which maps to available
                'available' => 'boolean',
                'status' => 'string|in:available,occupied,maintenance',
                'image_url' => 'nullable|url',
                'amenities' => 'nullable|array',
            ]);

            // map frontend keys
            if (isset($validated['pricePerNight'])) {
                $validated['price_per_night'] = $validated['pricePerNight'];
                unset($validated['pricePerNight']);
            }
            if (isset($validated['status'])) {
                $validated['available'] = $validated['status'] === 'available';
                unset($validated['status']);
            }

            $accommodation = Accommodation::create($validated);

            return response([
                'success' => true,
                'data' => $accommodation,
                'message' => 'Accommodation created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    /**
     * Display the specified accommodation
     */
    public function show(Accommodation $accommodation): Response
    {
        return response([
            'success' => true,
            'data' => $accommodation->load('reservations'),
            'message' => 'Accommodation retrieved successfully'
        ]);
    }

    /**
     * Update the specified accommodation
     */
    public function update(Request $request, Accommodation $accommodation): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'description' => 'nullable|string',
                'type' => 'string|in:standard,deluxe,suite,villa,room,bungalow',
                'capacity' => 'integer|min:1',
                'price_per_night' => 'numeric|min:0',
                // allow frontend aliases in update as well
                'pricePerNight' => 'numeric|min:0',
                'available' => 'boolean',
                'status' => 'string|in:available,occupied,maintenance',
                'image_url' => 'nullable|url',
                'amenities' => 'nullable|array',
            ]);

            // map frontend keys like store
            // capture old values for dynamic recalculation logic
            $oldPrice = $accommodation->price_per_night;
            $oldAvailable = $accommodation->available;

            // map frontend keys like store
            if (isset($validated['pricePerNight'])) {
                $validated['price_per_night'] = $validated['pricePerNight'];
                unset($validated['pricePerNight']);
            }
            if (isset($validated['status'])) {
                $validated['available'] = $validated['status'] === 'available';
                unset($validated['status']);
            }

            $accommodation->update($validated);

            // If price changed, recompute total_price for related reservations (pending/confirmed)
            if (array_key_exists('price_per_night', $validated) && $oldPrice != $accommodation->price_per_night) {
                $newPpn = $accommodation->price_per_night ?? 0;
                $affected = $accommodation->reservations()->whereIn('status', ['pending', 'confirmed'])->get();
                foreach ($affected as $res) {
                    $n = $res->number_of_nights ?? 1;
                    $res->update(['total_price' => $n * $newPpn]);
                }
            }

            // If availability toggled to false, move future confirmed reservations back to pending
            if (array_key_exists('available', $validated) && $oldAvailable !== ($validated['available'] ?? $oldAvailable)) {
                if (($validated['available'] ?? false) === false) {
                    $accommodation->reservations()
                        ->where('status', 'confirmed')
                        ->whereDate('check_in_date', '>=', now()->toDateString())
                        ->update(['status' => 'pending']);
                }
            }

            return response([
                'success' => true,
                'data' => $accommodation,
                'message' => 'Accommodation updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    /**
     * Remove the specified accommodation
     */
    public function destroy(Accommodation $accommodation): Response
    {
        $accommodation->delete();

        return response([
            'success' => true,
            'message' => 'Accommodation deleted successfully'
        ]);
    }

    /**
     * Get available accommodations for given dates
     */
    public function availableForDates(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
            ]);

            $available = Accommodation::availableForDates(
                $validated['check_in_date'],
                $validated['check_out_date']
            );

            return response([
                'success' => true,
                'data' => $available,
                'message' => 'Available accommodations retrieved successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }
}

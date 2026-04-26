<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccommodationController extends Controller
{
    public function index(): Response
    {
        $accommodations = Accommodation::all();

        return response([
            'success' => true,
            'data' => $accommodations,
            'message' => 'Accommodations retrieved successfully'
        ]);
    }

    public function store(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',

                // ✅ made optional to avoid frontend errors
                'type' => 'nullable|string|in:standard,deluxe,suite,villa',
                'capacity' => 'nullable|integer|min:1',

                // ✅ still required
                'price_per_night' => 'required|numeric|min:0',

                'available' => 'nullable|boolean',
                'image_url' => 'nullable|url',
                'amenities' => 'nullable|array',
            ]);

            // ✅ default values (important)
            $validated['type'] = $validated['type'] ?? 'standard';
            $validated['capacity'] = $validated['capacity'] ?? 1;
            $validated['available'] = $validated['available'] ?? true;

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

    public function show(Accommodation $accommodation): Response
    {
        return response([
            'success' => true,
            'data' => $accommodation->load('reservations'),
            'message' => 'Accommodation retrieved successfully'
        ]);
    }

    public function update(Request $request, Accommodation $accommodation): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'description' => 'nullable|string',
                'type' => 'nullable|string|in:standard,deluxe,suite,villa',
                'capacity' => 'nullable|integer|min:1',
                'price_per_night' => 'numeric|min:0',
                'available' => 'nullable|boolean',
                'image_url' => 'nullable|url',
                'amenities' => 'nullable|array',
            ]);

            $accommodation->update($validated);

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

    public function destroy(Accommodation $accommodation): Response
    {
        $accommodation->delete();

        return response([
            'success' => true,
            'message' => 'Accommodation deleted successfully'
        ]);
    }

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
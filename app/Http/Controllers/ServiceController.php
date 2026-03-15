<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::all();
        return response([
            'success' => true,
            'data' => $services,
            'message' => 'Services retrieved successfully'
        ]);
    }

    public function store(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'duration' => 'nullable|string',
                'bookingsToday' => 'sometimes|integer|min:0',
                'revenue' => 'sometimes|numeric|min:0',
            ]);

            $data = [
                'name' => $validated['name'],
                'category' => $validated['category'] ?? null,
                'price' => $validated['price'],
                'duration' => $validated['duration'] ?? null,
                'bookings_today' => $validated['bookingsToday'] ?? 0,
                'revenue' => $validated['revenue'] ?? 0,
            ];

            $service = Service::create($data);
            return response([
                'success' => true,
                'data' => $service,
                'message' => 'Service created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function show(Service $service): Response
    {
        return response([
            'success' => true,
            'data' => $service,
            'message' => 'Service retrieved successfully'
        ]);
    }

    public function update(Request $request, Service $service): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'category' => 'string',
                'price' => 'numeric|min:0',
                'duration' => 'string',
                'bookingsToday' => 'integer|min:0',
                'revenue' => 'numeric|min:0',
            ]);

            $data = [];
            if (isset($validated['name'])) {
                $data['name'] = $validated['name'];
            }
            if (isset($validated['category'])) {
                $data['category'] = $validated['category'];
            }
            if (isset($validated['price'])) {
                $data['price'] = $validated['price'];
            }
            if (isset($validated['duration'])) {
                $data['duration'] = $validated['duration'];
            }
            if (isset($validated['bookingsToday'])) {
                $data['bookings_today'] = $validated['bookingsToday'];
            }
            if (isset($validated['revenue'])) {
                $data['revenue'] = $validated['revenue'];
            }

            $service->update($data);
            return response([
                'success' => true,
                'data' => $service,
                'message' => 'Service updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function destroy(Service $service): Response
    {
        $service->delete();
        return response([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
}

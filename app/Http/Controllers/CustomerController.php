<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(): Response
    {
        $customers = Customer::all();
        return response([
            'success' => true,
            'data' => $customers,
            'message' => 'Customers retrieved successfully'
        ]);
    }

    public function store(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'nationality' => 'nullable|string',
                'totalStays' => 'sometimes|integer|min:0',
                'totalSpent' => 'sometimes|numeric|min:0',
                'lastVisit' => 'nullable|date',
            ]);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                // Accept `address` from clients but persist to the existing `nationality` column
                'nationality' => $validated['address'] ?? $validated['nationality'] ?? null,
                'total_stays' => $validated['totalStays'] ?? 0,
                'total_spent' => $validated['totalSpent'] ?? 0,
                'last_visit' => $validated['lastVisit'] ?? null,
            ];

            $customer = Customer::create($data);
            return response([
                'success' => true,
                'data' => $customer,
                'message' => 'Customer created successfully'
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
                'message' => 'Error creating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Customer $customer): Response
    {
        return response([
            'success' => true,
            'data' => $customer,
            'message' => 'Customer retrieved successfully'
        ]);
    }

    public function update(Request $request, Customer $customer): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'email' => 'email|unique:customers,email,' . $customer->id,
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'nationality' => 'nullable|string',
                'totalStays' => 'integer|min:0',
                'totalSpent' => 'numeric|min:0',
                'lastVisit' => 'nullable|date',
            ]);

            $data = [];
            if (isset($validated['name'])) {
                $data['name'] = $validated['name'];
            }
            if (isset($validated['email'])) {
                $data['email'] = $validated['email'];
            }
            if (isset($validated['phone'])) {
                $data['phone'] = $validated['phone'];
            }
            // Accept either address or nationality from clients; store in nationality column
            if (isset($validated['address'])) {
                $data['nationality'] = $validated['address'];
            } elseif (isset($validated['nationality'])) {
                $data['nationality'] = $validated['nationality'];
            }
            if (isset($validated['totalStays'])) {
                $data['total_stays'] = $validated['totalStays'];
            }
            if (isset($validated['totalSpent'])) {
                $data['total_spent'] = $validated['totalSpent'];
            }
            if (isset($validated['lastVisit'])) {
                $data['last_visit'] = $validated['lastVisit'];
            }

            $customer->update($data);

            return response([
                'success' => true,
                'data' => $customer,
                'message' => 'Customer updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function destroy(Customer $customer): Response
    {
        $customer->delete();
        return response([
            'success' => true,
            'message' => 'Customer deleted successfully'
        ]);
    }
}

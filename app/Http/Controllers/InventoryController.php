<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventoryController extends Controller
{
    public function index(): Response
    {
        $items = InventoryItem::all();
        return response([
            'success' => true,
            'data' => $items,
            'message' => 'Inventory items retrieved successfully'
        ]);
    }

    public function store(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'minStock' => 'sometimes|integer|min:0',
                'unit' => 'nullable|string',
            ]);

            $data = [
                'name' => $validated['name'],
                'category' => $validated['category'] ?? null,
                'quantity' => $validated['quantity'],
                'min_stock' => $validated['minStock'] ?? 0,
                'unit' => $validated['unit'] ?? null,
                'last_updated' => now(),
            ];

            $item = InventoryItem::create($data);
            return response([
                'success' => true,
                'data' => $item,
                'message' => 'Inventory item created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function show(InventoryItem $inventoryItem): Response
    {
        return response([
            'success' => true,
            'data' => $inventoryItem,
            'message' => 'Inventory item retrieved successfully'
        ]);
    }

    public function update(Request $request, InventoryItem $inventoryItem): Response
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'category' => 'nullable|string',
                'quantity' => 'integer|min:0',
                'minStock' => 'integer|min:0',
                'unit' => 'nullable|string',
            ]);

            $data = [];
            if (isset($validated['name'])) {
                $data['name'] = $validated['name'];
            }
            if (isset($validated['category'])) {
                $data['category'] = $validated['category'];
            }
            if (isset($validated['quantity'])) {
                $data['quantity'] = $validated['quantity'];
            }
            if (isset($validated['minStock'])) {
                $data['min_stock'] = $validated['minStock'];
            }
            if (isset($validated['unit'])) {
                $data['unit'] = $validated['unit'];
            }

            // Always update the last_updated timestamp
            $data['last_updated'] = now();

            $inventoryItem->update($data);
            return response([
                'success' => true,
                'data' => $inventoryItem,
                'message' => 'Inventory item updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function destroy(InventoryItem $inventoryItem): Response
    {
        $inventoryItem->delete();
        return response([
            'success' => true,
            'message' => 'Inventory item deleted successfully'
        ], 200);
    }
}

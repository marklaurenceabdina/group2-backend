<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $payments = Payment::all();
        return response([
            'success' => true,
            'data' => $payments,
            'message' => 'Payments retrieved successfully'
        ]);
    }

    public function store(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'guestName' => 'required|string|max:255',
                'reservationId' => 'nullable|integer|exists:reservations,id',
                'amount' => 'required|numeric|min:0',
                'method' => 'required|string|in:card,cash,transfer',
                'status' => 'required|string|in:pending,completed,refunded',
                'date' => 'nullable|date',
            ]);

            $data = [
                'guest_name' => $validated['guestName'],
                'reservation_id' => $validated['reservationId'] ?? null,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'status' => $validated['status'],
                'date' => $validated['date'] ?? now(),
            ];

            $payment = Payment::create($data);
            return response([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment recorded successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function show(Payment $payment): Response
    {
        return response([
            'success' => true,
            'data' => $payment,
            'message' => 'Payment retrieved successfully'
        ]);
    }

    public function update(Request $request, Payment $payment): Response
    {
        try {
            $validated = $request->validate([
                'guestName' => 'string|max:255',
                'reservationId' => 'integer|exists:reservations,id',
                'amount' => 'numeric|min:0',
                'method' => 'string|in:card,cash,transfer',
                'status' => 'string|in:pending,completed,refunded',
                'date' => 'nullable|date',
            ]);

            $data = [];
            if (isset($validated['guestName'])) {
                $data['guest_name'] = $validated['guestName'];
            }
            if (isset($validated['reservationId'])) {
                $data['reservation_id'] = $validated['reservationId'];
            }
            if (isset($validated['amount'])) {
                $data['amount'] = $validated['amount'];
            }
            if (isset($validated['method'])) {
                $data['method'] = $validated['method'];
            }
            if (isset($validated['status'])) {
                $data['status'] = $validated['status'];
            }
            if (isset($validated['date'])) {
                $data['date'] = $validated['date'];
            }

            $payment->update($data);
            return response([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
    }

    public function destroy(Payment $payment): Response
    {
        $payment->delete();
        return response([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}

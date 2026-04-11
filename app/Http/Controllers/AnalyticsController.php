<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function today()
    {
        $today = Carbon::today();

        // Today's revenue from payments
        $totalRevenue = Payment::whereDate('date', $today)
            ->where('status', 'paid')
            ->sum('amount');

        // Total bookings today (reservations created today)
        $totalBookings = Reservation::whereDate('created_at', $today)->count();

        return response()->json([
            'totalRevenue' => (float) $totalRevenue,
            'totalBookings' => $totalBookings,
        ]);
    }
}

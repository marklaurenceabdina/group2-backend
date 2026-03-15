<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reservation;
use Carbon\Carbon;

echo "Starting backfill of reservation check-in times...\n";
$count = 0;
foreach (Reservation::all() as $r) {
    try {
        $ci = Carbon::parse($r->check_in_date);
        $created = Carbon::parse($r->created_at);
        $hms = $ci->format('H:i:s');
        if (in_array($hms, ['00:00:00', '08:00:00'])) {
            $ci->setTime($created->hour, $created->minute, $created->second);
            $r->check_in_date = $ci->toDateTimeString();
            $r->save();
            echo "Updated reservation {$r->id} -> {$r->check_in_date}\n";
            $count++;
        }
    } catch (\Exception $e) {
        echo "Skipped reservation {$r->id}: {$e->getMessage()}\n";
    }
}

echo "Backfill complete. Updated {$count} reservations.\n";

return 0;

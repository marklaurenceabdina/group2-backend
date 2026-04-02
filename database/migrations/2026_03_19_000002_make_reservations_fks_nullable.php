<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeReservationsFksNullable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop existing foreign keys so we can re-create them with SET NULL behavior.
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['accommodation_id']);

            // Make the FK columns nullable so they can be null when related records are deleted.
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('accommodation_id')->nullable()->change();

            // Re-create FKs with ON DELETE SET NULL to preserve historical reservations.
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('accommodation_id')->references('id')->on('accommodations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['accommodation_id']);

            // Revert columns to not nullable.
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->unsignedBigInteger('accommodation_id')->nullable(false)->change();

            // Recreate the original cascading deletes.
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('accommodation_id')->references('id')->on('accommodations')->onDelete('cascade');
        });
    }
}

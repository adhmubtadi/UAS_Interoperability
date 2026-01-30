<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Foreign Keys (UUID)
            $table->uuid('user_id');
            $table->uuid('barber_id');
            $table->uuid('service_id');
            
            // Booking Details
            $table->date('booking_date');
            $table->time('booking_time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'canceled'])->default('pending');
            
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('barber_id')
                  ->references('id')
                  ->on('barbers')
                  ->onDelete('cascade');
            
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('cascade');
            
            // Indexes untuk performance
            $table->index('user_id');
            $table->index('barber_id');
            $table->index('service_id');
            $table->index('booking_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

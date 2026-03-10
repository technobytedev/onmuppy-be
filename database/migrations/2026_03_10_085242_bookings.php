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
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();     // customer
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();   // service provider
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();  // what service
            $table->enum('status', [
                'pending',      // customer booked, waiting for vendor confirmation
                'confirmed',    // vendor accepted
                'in_progress',  // service is ongoing
                'completed',    // done
                'cancelled',    // cancelled by either party
                'no_show',      // customer didn't show up
            ])->default('pending');
            $table->enum('location_type', [
                'customer_address',  // provider travels to customer (home service)
                'vendor_address',    // customer goes to provider
            ])->default('customer_address');
            $table->text('address')->nullable();           // where service happens
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('scheduled_at');             // appointment date & time
            $table->integer('duration_minutes')->nullable();
            $table->decimal('agreed_price', 10, 2)->nullable();
            $table->text('customer_notes')->nullable();    // special requests
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
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

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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('name');                     // "Full Body Massage", "Aircon Repair"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('price_type', [
                'fixed',        // flat rate — e.g. ₱500 per session
                'hourly',       // e.g. ₱200/hr
                'starting_at',  // e.g. starts at ₱300 (varies)
                'free_quote',   // price discussed upon inquiry
            ])->default('fixed');
            $table->integer('duration_minutes')->nullable();  // estimated service duration
            $table->string('category');               // wellness, repair, tech, beauty, etc.
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_home_service')->default(true);  // goes to customer's location
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

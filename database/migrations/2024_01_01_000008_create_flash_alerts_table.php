<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('flash_price', 10, 2)->nullable();
            $table->decimal('alert_radius_km', 5, 2)->default(2.00);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_alerts');
    }
};

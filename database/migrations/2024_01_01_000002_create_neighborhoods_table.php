<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('region')->nullable();
            $table->decimal('center_lat', 10, 7);
            $table->decimal('center_lng', 10, 7);
            $table->decimal('radius_km', 5, 2)->default(2.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('neighborhoods');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('type', [
                'fixed',
                'mobile',
                'home_based',
                'freelance',
            ])->default('fixed');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('address')->nullable();
            $table->decimal('service_radius_km', 5, 2)->default(5.00);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_open')->default(false);
            $table->foreignId('neighborhood_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};

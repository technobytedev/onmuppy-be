<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->decimal('latitude', 10, 7)->nullable()->after('phone');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('neighborhood')->nullable()->after('longitude');
            $table->enum('role', ['user', 'vendor', 'courier'])->default('user')->after('neighborhood');
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'latitude', 'longitude', 'neighborhood', 'role', 'is_active']);
        });
    }
};

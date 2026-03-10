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
        // Weekly availability template
        Schema::create('vendor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->enum('day_of_week', [
                'monday', 'tuesday', 'wednesday',
                'thursday', 'friday', 'saturday', 'sunday'
            ]);
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['vendor_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_schedules');
    }
};

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
        // Work samples — before/after photos, certifications, etc.
        Schema::create('vendor_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();           // "Aircon Installation", "Swedish Massage"
            $table->text('description')->nullable();
            $table->string('image');                       // photo of work
            $table->enum('type', [
                'work_sample',      // photo of completed work
                'certification',    // license or cert photo
                'before_after',     // transformation photo
            ])->default('work_sample');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()
                  ->references('id')->on('users')->nullOnDelete();
            $table->json('items');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->enum('status', [
                'pending',
                'accepted',
                'picked_up',
                'in_transit',
                'delivered',
                'cancelled',
            ])->default('pending');
            $table->text('delivery_address');
            $table->decimal('delivery_lat', 10, 7);
            $table->decimal('delivery_lng', 10, 7);
            $table->text('notes')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};

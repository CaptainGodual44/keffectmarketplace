<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lsl_delivery_receipts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->uuid('object_uuid');
            $table->timestamp('delivered_at');
            $table->json('confirmation_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lsl_delivery_receipts');
    }
};

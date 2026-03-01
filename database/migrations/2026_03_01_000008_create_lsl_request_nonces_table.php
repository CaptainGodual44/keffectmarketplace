<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lsl_request_nonces', function (Blueprint $table): void {
            $table->id();
            $table->uuid('object_uuid');
            $table->string('nonce', 128);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['object_uuid', 'nonce']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lsl_request_nonces');
    }
};

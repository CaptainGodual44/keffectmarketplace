<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lsl_objects', function (Blueprint $table): void {
            $table->id();
            $table->uuid('object_uuid')->unique();
            $table->uuid('owner_uuid');
            $table->string('shared_secret_hash');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lsl_objects');
    }
};

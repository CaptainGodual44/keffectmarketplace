<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lsl_objects', function (Blueprint $table): void {
            $table->text('shared_secret_encrypted')->nullable()->after('shared_secret_hash');
        });

        Schema::create('lsl_request_nonces', function (Blueprint $table): void {
            $table->id();
            $table->uuid('object_uuid');
            $table->string('nonce', 128);
            $table->unsignedBigInteger('request_timestamp');
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->unique(['object_uuid', 'nonce']);
        });

        Schema::table('payment_intents', function (Blueprint $table): void {
            $table->string('provider_txn_id')->nullable()->unique()->after('status');
        });

        Schema::table('payment_webhook_events', function (Blueprint $table): void {
            $table->uuid('intent_uuid')->after('event_id');
            $table->unique(['intent_uuid', 'provider_txn_id']);
            $table->unique('payload_hash');
        });
    }

    public function down(): void
    {
        Schema::table('payment_webhook_events', function (Blueprint $table): void {
            $table->dropUnique(['intent_uuid', 'provider_txn_id']);
            $table->dropUnique(['payload_hash']);
            $table->dropColumn('intent_uuid');
        });

        Schema::table('payment_intents', function (Blueprint $table): void {
            $table->dropUnique(['provider_txn_id']);
            $table->dropColumn('provider_txn_id');
        });

        Schema::dropIfExists('lsl_request_nonces');

        Schema::table('lsl_objects', function (Blueprint $table): void {
            $table->dropColumn('shared_secret_encrypted');
        });
    }
};

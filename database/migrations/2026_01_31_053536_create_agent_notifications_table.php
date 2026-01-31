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
        Schema::create('agent_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agent_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50); // 'complaint', 'shipment', 'account', 'card'
            $table->string('action', 20); // 'created', 'updated'
            $table->string('notifiable_type');
            $table->uuid('notifiable_id');
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['agent_id', 'read_at']);
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_notifications');
    }
};

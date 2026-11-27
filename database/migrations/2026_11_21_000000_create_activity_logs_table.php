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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 36)->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('guard', 20)->nullable();
            $table->string('method', 10);
            $table->text('url');
            $table->string('route_name')->nullable();
            $table->unsignedSmallInteger('status_code')->index();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('payload')->nullable();
            $table->string('error')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

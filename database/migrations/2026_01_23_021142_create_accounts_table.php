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
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUuid('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->string('bank_name');
            $table->string('branch')->nullable();
            $table->string('account_number')->unique();
            $table->date('opening_date')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['aktif', 'bermasalah', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

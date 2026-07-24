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
        Schema::create('company_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('agent_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name')->index();
            $table->string('bank_name')->index();
            $table->string('branch')->nullable();
            $table->string('account_number')->unique();
            $table->date('opening_date')->nullable();
            $table->date('expired_on')->nullable();
            $table->text('mobile_banking')->nullable();
            $table->text('note')->nullable();
            $table->string('cover_buku')->nullable();
            $table->enum('status', ['aktif', 'bermasalah', 'nonaktif'])->default('aktif')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_accounts');
    }
};

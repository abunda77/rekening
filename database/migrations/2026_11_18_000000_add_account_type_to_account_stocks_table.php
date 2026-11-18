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
        Schema::table('account_stocks', function (Blueprint $table) {
            $table->string('account_type')->default('personal')->after('bank_name')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_stocks', function (Blueprint $table) {
            $table->dropIndex(['account_type']);
            $table->dropColumn('account_type');
        });
    }
};

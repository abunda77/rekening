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
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignUuid('agent_supplier_id')->nullable()->after('agent_id')->constrained()->nullOnDelete();
        });

        Schema::table('company_accounts', function (Blueprint $table) {
            $table->foreignUuid('agent_supplier_id')->nullable()->after('agent_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['agent_supplier_id']);
            $table->dropColumn('agent_supplier_id');
        });

        Schema::table('company_accounts', function (Blueprint $table) {
            $table->dropForeign(['agent_supplier_id']);
            $table->dropColumn('agent_supplier_id');
        });
    }
};

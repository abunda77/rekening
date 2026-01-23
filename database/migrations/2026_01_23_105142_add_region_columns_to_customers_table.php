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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('province_code')->nullable()->after('address');
            $table->string('province')->nullable()->after('province_code');
            $table->string('regency_code')->nullable()->after('province');
            $table->string('regency')->nullable()->after('regency_code');
            $table->string('district_code')->nullable()->after('regency');
            $table->string('district')->nullable()->after('district_code');
            $table->string('village_code')->nullable()->after('district');
            $table->string('village')->nullable()->after('village_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'province_code', 'province',
                'regency_code', 'regency',
                'district_code', 'district',
                'village_code', 'village',
            ]);
        });
    }
};

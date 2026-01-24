<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Customer::truncate();
        Schema::enableForeignKeyConstraints();
        // Create specific customer from Planning.md
        Customer::create([
            'nik' => '3213096212020011',
            'full_name' => 'Wimpi Gindasari',
            'mother_name' => 'Ibu Gindasari',
            'email' => 'wimpi@example.com',
            'phone_number' => '081234567890',
            'address' => 'Jl. Contoh No. 123, Subang',
            'note' => 'Customer prioritas',
        ]);

        // Create additional random customers
        Customer::factory(10)->create();
    }
}

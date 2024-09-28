<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        Customer::create([
            'name' => 'Kasir',
            'phone' => '08123456789',
        ]);
    }
}

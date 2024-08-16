<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_options')->insert([
            [
                'name' => 'COD',
                'description' => '',
                'type' => 'Cash on delivery', 
                'is_active' => 0
            ],
            [
                'name' => 'Direct Bank Transfer',
                'description' => '',
                'type' => 'direct bank transfer', 
                'is_active' => 0
            ],
            [
                'name' => 'PAYNOW',
                'description' => '',
                'type' => 'paynow', 
                'is_active' => 0
            ]
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSubTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_sub_types')->insert([
            [
                'payment_option_id' => '3',
                'name' => 'Mobile Number',
                'description' => '', 
                'is_active' => 0
            ],
            [
                'payment_option_id' => '3',
                'name' => 'UEN',
                'description' => '', 
                'is_active' => 0
            ],
            [
                'payment_option_id' => '3',
                'name' => 'QR Code',
                'description' => '', 
                'is_active' => 0
            ]
        ]);
    }
}

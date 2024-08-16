<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
        {
            DB::table('headers')->insert([
                'header_logo_path' => 'assets\header_images\Logo.png',
                'header_logo_name' => 'Logo.png',
                'header_logo_extension' =>'png',
                'header_logo_size'=>'1024',
                'approved_header_logo_name' => 'Logo.png',
                'approved_header_logo_path' => 'assets\header_images\Logo.png',
                'approved_header_logo_extension' => 'png',
                'approved_header_logo_size' => '1024',
                'is_approved' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
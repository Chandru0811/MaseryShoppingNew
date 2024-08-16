<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contact_us')->insert([
            'phone' => '+1 (323) 9847 3847 383, +1 (434) 5466 5467 443',
            'email' => 'infoyour@gmail.com, demoinfoemail@gmail.com',
            'address' => '4517 Washington Ave. Manchester, Road, 234 Kentucky USA',
            'timing' => 'Mon - Sat: 9am - 11pm, Sunday: 11am - 5pm',
            'maplink' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3886.597764489138!2d80.25717047359075!3d13.061256012909292!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a526615d71d8163%3A0x234b611e7d1f61ea!2sShakti%20Towers%2C%20Sakthi%20Tower%20Ln%2C%20Anna%20Salai%2C%20Thousand%20Lights%2C%20Chennai%2C%20Tamil%20Nadu%20600002!5e0!3m2!1sen!2sin!4v1717675377721!5m2!1sen!2sin',
            'heading' => 'Get In Touch With Us',
            'content' => 'Duis gravida augue velit, eu dignissim felis posuere quis. Integer ante urna, gravida nec est tincidunt, orci at turpis gravida. Phasellus egestas odio.',
        ]);
    }
}

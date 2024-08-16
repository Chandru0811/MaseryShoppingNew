<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
        {
            DB::table('footers')->insert([
                'footer_logo_path' => 'assets\footer_images\LogoLight.png',
                // 'header_logo_name' => 'Logo.png',
                // 'header_logo_extension' =>'png',
                // 'header_logo_size'=>'1024',
                'about_text'=>'Etoshi is an exciting contemporary brand which focuses on high-quality products graphics with a British style',
                'whatsapp_link' => 'https://www.whatsapp.com',
                'facebook_link' => 'http://www.facebook.com',
                'twitter_link' => 'http://www.twitter.com',
                'instagram_link' => 'http://www.instagram.com',
                'tiktok_link' => 'http://www.tiktok.com',
                'linkedin_link' => 'http://www.linkedin.com',
                'googleplay_link' => 'http://www.googleplay.com',
                'appstore_link' => 'http://appstore.com',
                'mail' => 'masery@gmail.com',
                'phone' => '9187338281',
                'address' => '4517 Washington Ave. Manchester,Road, 234 Kentucky...',
                'copyrights' => 'Copy rights @masery.2024',
                'approved_footer_logo_path' => 'assets\footer_images\LogoLight.png',
                'approved_about_text' => 'Etoshi is an exciting contemporary brand which focuses on high-quality products graphics with a British style',
                'approved_whatsapp_link' => 'https://www.whatsapp.com',
                'approved_facebook_link' => 'http://www.facebook.com',
                'approved_twitter_link' => 'http://www.twitter.com',
                'approved_instagram_link' => 'http://www.instagram.com',
                'approved_tiktok_link' => 'http://www.tiktok.com',
                'approved_linkedin_link' => 'http://www.linkedin.com',
                'approved_googleplay_link' => 'http://www.googleplay.com',
                'approved_appstore_link' => 'http://appstore.com',
                'approved_mail' => 'masery@gmail.com',
                'approved_phone' => '9187338281',
                'approved_address' => '4517 Washington Ave. Manchester,Road, 234 Kentucky...',
                'approved_copyrights' => 'Copy rights @masery.2024',
                'is_approved' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
}

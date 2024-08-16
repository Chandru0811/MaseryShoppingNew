<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;

    protected $table = 'footers';

    protected $fillable = [
        'footer_logo_path',
        'about_text',
        'whatsapp_link',
        'facebook_link',
        'twitter_link',
        'instagram_link',
        'tiktok_link',
        'linkedin_link',
        'googleplay_link',
        'appstore_link',
        'mail',
        'phone',
        'address',
        'copyrights',
    ];

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)
                     ->select([
                        'approved_footer_logo_path as footer_logo_path',
                        'approved_about_text as about_text',
                        'approved_whatsapp_link as whatsapp_link',
                        'approved_facebook_link as facebook_link',
                        'approved_twitter_link as twitter_link',
                        'approved_instagram_link as instagram_link',
                        'approved_tiktok_link as tiktok_link',
                        'approved_linkedin_link as linkedin_link',
                        'approved_googleplay_link as googleplay_link',
                        'approved_appstore_link as appstore_link',
                        'approved_mail as mail',
                        'approved_phone as phone',
                        'approved_address as address',
                        'approved_copyrights as copyrights',
                     ]);
    }
}


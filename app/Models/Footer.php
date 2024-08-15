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

    public function approve()
    {
        $this->approved_footer_logo_path = $this->footer_logo_path;
        $this->approved_about_text = $this->about_text;
        $this->approved_whatsapp_link = $this->whatsapp_link;
        $this->approved_facebook_link = $this->facebook_link;
        $this->approved_twitter_link = $this->twitter_link;
        $this->approved_instagram_link = $this->instagram_link;
        $this->approved_tiktok_link = $this->tiktok_link;
        $this->approved_linkedin_link = $this->linkedin_link;
        $this->approved_googleplay_link = $this->googleplay_link;
        $this->approved_appstore_link = $this->appstore_link;
        $this->approved_mail = $this->mail;
        $this->approved_phone = $this->phone;
        $this->approved_address = $this->address;
        $this->approved_copyrights = $this->copyrights;
        $this->is_approved = true;
        $this->save();
    }
}


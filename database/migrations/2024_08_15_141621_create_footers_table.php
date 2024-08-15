<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->string('footer_logo_path');
            $table->text('about_text');
            $table->string('whatsapp_link');
            $table->string('facebook_link');
            $table->string('twitter_link');
            $table->string('instagram_link');
            $table->string('tiktok_link');
            $table->string('linkedin_link');
            $table->string('googleplay_link');
            $table->string('appstore_link');
            $table->string('mail');
            $table->string('phone');
            $table->string('address');
            $table->string('copyrights');

            // Approved fields
            $table->string('approved_footer_logo_path')->nullable();
            $table->text('approved_about_text')->nullable();
            $table->string('approved_whatsapp_link')->nullable();
            $table->string('approved_facebook_link')->nullable();
            $table->string('approved_twitter_link')->nullable();
            $table->string('approved_instagram_link')->nullable();
            $table->string('approved_tiktok_link')->nullable();
            $table->string('approved_linkedin_link')->nullable();
            $table->string('approved_googleplay_link')->nullable();
            $table->string('approved_appstore_link')->nullable();
            $table->string('approved_mail')->nullable();
            $table->string('approved_phone')->nullable();
            $table->string('approved_address')->nullable();
            $table->string('approved_copyrights')->nullable();
            $table->boolean('is_approved')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('footers');
    }
};

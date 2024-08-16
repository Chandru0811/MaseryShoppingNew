<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('timing');
            $table->string('maplink');
            $table->string('heading');
            $table->text('content');

            // Approved fields
            $table->string('approved_phone')->nullable();
            $table->string('approved_email')->nullable();
            $table->string('approved_address')->nullable();
            $table->string('approved_timing')->nullable();
            $table->string('approved_maplink')->nullable();
            $table->string('approved_heading')->nullable();
            $table->text('approved_content')->nullable();
            $table->boolean('is_approved')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_us');
    }
};

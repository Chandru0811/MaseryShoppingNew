<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('headers', function (Blueprint $table) {
            $table->id();
            $table->string('header_logo_name');
            $table->string('header_logo_path');
            $table->string('header_logo_extension');
            $table->integer('header_logo_size');
            // Approved fields
            $table->string('approved_header_logo_name')->nullable();
            $table->string('approved_header_logo_path')->nullable();
            $table->string('approved_header_logo_extension')->nullable();
            $table->integer('approved_header_logo_size')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('headers');
    }
};

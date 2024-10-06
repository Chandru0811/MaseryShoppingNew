<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_sub_groups_one', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_sub_group_id')->unsigned();
            $table->string('name',200)->unique();
            $table->string('slug',200)->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_sub_group_id')->references('id')->on('category_sub_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_sub_groups_one');
    }
};

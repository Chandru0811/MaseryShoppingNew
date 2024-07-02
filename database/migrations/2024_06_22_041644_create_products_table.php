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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('brand_id')->unsigned()->nullable();
            $table->string('brand')->nullable();
            $table->string('name');
            $table->string('model_number')->nullable();
            $table->string('mpn')->nullable();
            $table->string('gtin')->nullable();
            $table->string('gtin_type')->nullable();
            $table->longtext('description')->nullable();
            $table->decimal('min_price', 20, 6)->default(0)->nullable();
            $table->decimal('max_price', 20, 6)->nullable();
            $table->boolean('requires_shipping')->default(1)->nullable();
            $table->string('slug')->unique();
            $table->bigInteger('sale_count')->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

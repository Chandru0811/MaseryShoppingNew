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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('brand_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('sku', 200);
            $table->enum('condition', ['New', 'Used', 'Refurbished']);
            $table->text('condition_note')->nullable();
            $table->longtext('description')->nullable();
            $table->text('key_features')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('damaged_quantity')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->decimal('purchase_price', 20, 6)->nullable();
            $table->decimal('sale_price', 20, 6);
            $table->decimal('offer_price', 20, 6)->nullable();
            $table->timestamp('offer_start')->nullable();
            $table->timestamp('offer_end')->nullable();
            $table->decimal('shipping_weight', 20, 2)->nullable();
            $table->boolean('free_shipping')->nullable();
            $table->timestamp('available_from')->useCurrent();
            $table->integer('min_order_quantity')->default(1);
            $table->string('slug', 200)->unique();
            $table->text('linked_items')->nullable();
            // $table->text('meta_title')->nullable();
            // $table->longtext('meta_description')->nullable();
            $table->boolean('stuff_pick')->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

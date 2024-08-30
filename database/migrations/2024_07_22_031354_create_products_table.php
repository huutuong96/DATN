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
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('infomation')->nullable();
            $table->integer('price');
            $table->integer('sale_price')->nullable();
            $table->string('image')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('sold_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('parent_id')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('shop_id');

            $table->foreign('category_id')->references('id')->on('categori_shops')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
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

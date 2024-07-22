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
        Schema::create('product_to_shops', function (Blueprint $table) {
            $table->id();
            $table->string('url_share')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shop_id');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_to_shops');
    }
};

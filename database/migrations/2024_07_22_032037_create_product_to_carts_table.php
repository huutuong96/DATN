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
        Schema::create('product_to_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->default(1);
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');

            $table->foreign('cart_id')->references('id')->on('cart_to_users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_to_carts');
    }
};

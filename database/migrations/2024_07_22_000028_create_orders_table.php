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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('ship_id');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_id');

            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('ship_id')->references('id')->on('ships')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

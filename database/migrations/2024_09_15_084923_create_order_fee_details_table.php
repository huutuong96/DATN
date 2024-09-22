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
        Schema::create('order_fee_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('platform_fee_id');
            $table->Integer('amount');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('platform_fee_id')->references('id')->on('platform_fees')->onDelete('restrict')->onUpdate('restrict');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_fee_details');
    }
};

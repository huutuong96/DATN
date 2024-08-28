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
        Schema::create('voucher_to_shops', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('condition')->nullable();
            $table->float('ratio')->nullable();
            $table->string('code');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('shop_id');

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_to_shops');
    }
};

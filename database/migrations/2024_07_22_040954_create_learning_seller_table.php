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
        Schema::create('learning_seller', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1);
            $table->integer('create_by');
            $table->integer('update_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('learn_id');
            $table->unsignedBigInteger('shop_id');

            $table->foreign('learn_id')->references('id')->on('learns')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_seller');
    }
};

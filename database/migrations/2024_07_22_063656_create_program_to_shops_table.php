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
        Schema::create('program_to_shops', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('shop_id');

            $table->foreign('program_id')->references('id')->on('programme_details')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_to_shops');
    }
};

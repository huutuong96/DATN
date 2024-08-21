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
        Schema::create('categori_shops', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->string('title');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->integer('status')->default(1);
            $table->integer('parent_id')->nullable();
            $table->integer('create_by');
            $table->integer('update_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('category_id_main');
            $table->unsignedBigInteger('shop_id');

            $table->foreign('category_id_main')->references('id')->on('categories')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categori_shops');
    }
};

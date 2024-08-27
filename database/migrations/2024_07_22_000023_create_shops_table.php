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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Owner_id');
            $table->string('shop_name');
            $table->string('slug');
            $table->string('pick_up_address');
            $table->string('image')->nullable();
            $table->string('cccd');
            $table->integer('status')->default(1);
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('tax_id');

            $table->foreign('Owner_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('tax_id')->references('id')->on('taxs')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};

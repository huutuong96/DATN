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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullalbe();
            $table->integer('status')->default(1);
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->timestamps();
            // $table->unsignedBigInteger('user_id');
            $table->string('code');

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            // $table->foreign('code')->references('id')->on('voucher_to_main')->onDelete('restrict')->onUpdate('restrict');
            // $table->foreign('code')->references('id')->on('voucher_to_shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};

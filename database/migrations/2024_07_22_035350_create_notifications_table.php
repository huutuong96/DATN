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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('id_notification');

            $table->foreign('user_id')->references('id')->on('voucher_to_main')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_notification')->references('id')->on('notification_to_main')->onDelete('restrict')->onUpdate('restrict');
            // $table->foreign('id_notification')->references('id')->on('notification_to_shops')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

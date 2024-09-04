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
        Schema::create('message_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mes_id');
            $table->text('content')->nullable();
            $table->integer('status')->default(1);
            $table->string('send_by');
            $table->timestamps();

            $table->foreign('mes_id')->references('id')->on('messages')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_details');
    }
};

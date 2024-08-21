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
        Schema::create('role_premissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('premission_id');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('premission_id')->references('id')->on('premissions')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_premissions');
    }
};

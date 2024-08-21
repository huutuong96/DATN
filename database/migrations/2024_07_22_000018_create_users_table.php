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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('password');
            $table->integer('phone')->nullable();
            $table->string('email');
            $table->text('description')->nullable();
            $table->integer('point')->default(0);
            $table->string('genre')->nullable();
            $table->date('datebirth')->nullable();
            $table->string('avatar')->nullable();
            $table->string('refesh_token')->nullable();
            $table->datetime('login_at');
            $table->timestamps();
            $table->unsignedBigInteger('rank_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('address_id');

            $table->foreign('rank_id')->references('id')->on('ranks')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('address_id')->references('id')->on('address')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

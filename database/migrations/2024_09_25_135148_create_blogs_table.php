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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('title');
            $table->text('description');
            $table->longText('content');
            $table->string('slug');
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            // $table->is_deleted(); 

            $table->foreign('create_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
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
        Schema::create('support_main', function (Blueprint $table) {
            $table->id();
            $table->string('content')->nullable();
            $table->integer('status')->default(1);
            $table->integer('index')->nullable();
            $table->integer('create_by');
            $table->integer('update_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('category_support_id');

            $table->foreign('category_support_id')->references('id')->on('categories_support_main')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_main');
    }
};

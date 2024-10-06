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
        Schema::create('refund_items', function (Blueprint $table) {
            $table->id(); // ID của dòng hoàn tiền sản phẩm
            $table->unsignedBigInteger('refund_id'); // Khóa ngoại liên kết đến bảng refunds
            $table->unsignedBigInteger('order_detail_id'); // Khóa ngoại liên kết đến bảng order_details
            $table->integer('quantity'); // Số lượng sản phẩm hoàn tiền
            $table->integer('refund_amount'); // Số tiền hoàn cho sản phẩm

            // Khóa ngoại
            $table->foreign('refund_id')->references('id')->on('refunds')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('cascade')->onUpdate('cascade');
            
            $table->timestamps(); // Thời gian tạo và cập nhật
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_items');
    }
};

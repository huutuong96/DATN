<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // Sử dụng unsignedBigInteger để liên kết với id trong bảng post_categories
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->text('slug')->unique();;
            $table->unsignedBigInteger('create_by'); // ID của người tạo
            $table->unsignedBigInteger('updated_by')->nullable(); // ID của người cập nhật
            $table->boolean('is_deleted')->default(false); // Trạng thái xóa
            $table->timestamps(); // Các trường created_at và updated_at

            // Thiết lập khóa ngoại cho post_id
            $table->foreign('post_id')->references('id')->on('post_categories')->onDelete('cascade'); // Nếu xóa bản ghi trong post_categories, các bản ghi liên quan trong blogs cũng sẽ bị xóa
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}

<?php

namespace App\Jobs;

use Cloudinary\Cloudinary;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imagePaths;
    // protected $productId;

    public function __construct($images)
    {
        $this->imagePaths = [];
        foreach ($images as $image) {
            $path = $image->store('temp'); // Lưu tệp vào thư mục tạm thời
            $this->imagePaths[] = $path;
        }
        // $this->productId = $productId;
    }

    public function handle()
    {
        $cloudinary = new Cloudinary();
        foreach ($this->imagePaths as $path) {
            $realPath = Storage::path($path); // Lấy đường dẫn thực tế của tệp
            $uploadedImage = $cloudinary->uploadApi()->upload($realPath);
            $imageUrl = $uploadedImage['secure_url'];
            // Image::create([
            //     'product_id' => $this->productId ?? null,
            //     'url' => $imageUrl,
            //     'status' => 1,
            // ]);
            Storage::delete($path); // Xóa tệp tạm thời sau khi tải lên
        }
        return $imageUrl;
    }
}

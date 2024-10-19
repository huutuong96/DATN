<?php

namespace App\Jobs;

use Cloudinary\Cloudinary;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $imagePaths;
    protected $imageUrl;
    public function __construct($images)
    {
        $this->imagePaths = [];
        foreach ($images as $image) {
            $path = $image->store('temp'); // Lưu tệp vào thư mục tạm thời
            $this->imagePaths[] = $path;
        }
        // $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cloudinary = new Cloudinary();
        $imageUrl = [];
        $images = [];
        $images = $request->file('images');
            foreach ($images as $image) {
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $imageUrl[] = $uploadedImage['secure_url'];
                Storage::delete($path); // Xóa tệp tạm thời sau khi tải lên
            }
    }
}

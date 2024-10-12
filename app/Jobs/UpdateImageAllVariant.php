<?php

namespace App\Jobs;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Storage;
use App\Models\product_variants;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class UpdateImageAllVariant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $imagePaths;
    protected $imageUrl;
    protected $variant_ids;
    public function __construct($images, $variant_ids)
    {
        $this->imagePaths = [];
        foreach ($images as $image) {
            $path = $image->store('temp'); // Lưu tệp vào thư mục tạm thời
            $this->imagePaths[] = $path;
        }
        $this->variant_ids = $variant_ids ?? []; // Khởi tạo $variant_ids hoặc mảng rỗng nếu null
    }

    /**
     * Execute the job.
     */
    public function handle(Cloudinary $cloudinary): void
    {
        $imageUrl = [];

        foreach ($this->imagePaths as $path) {
            $uploadedImage = $cloudinary->uploadApi()->upload(storage_path('app/' . $path));
            $imageUrl[] = $uploadedImage['secure_url'];
            Storage::delete($path); // Xóa tệp tạm thời sau khi tải lên
        }

        $jsonImageData = json_encode($imageUrl);
        product_variants::whereIn('id', $this->variant_ids)
            ->update(['images' => $jsonImageData]);
    }
}

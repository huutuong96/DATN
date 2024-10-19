<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use App\Models\Image;

class ImageUploadService
{
    protected $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function uploadImages($images, $productId)
    {
        foreach ($images as $image) {
            $uploadedImage = $this->cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
            Image::create([
                'product_id' => $productId,
                'url' => $imageUrl,
                'status' => 1,
            ]);
        }
    }
}

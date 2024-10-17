<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\configModel;
use Cloudinary\Cloudinary;

class changeConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image_logo;
    protected $image_footer;
    protected $image_icon;
    protected $thumbnail;
    public function __construct($image_logo, $image_footer, $image_icon, $thumbnail, $main_color)
    {
        $this->image_logo = $image_logo;
        $this->image_footer = $image_footer;
        $this->image_icon = $image_icon;
        $this->thumbnail = $thumbnail;
        $this->main_color = $main_color;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cloudinary = new Cloudinary();
        if ($this->image_logo) {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
            $config = configModel::update([
                'logo_header', $imageUrl
            ]);
            Storage::delete($path); // Xóa tệp tạm thời sau khi tải lên
        }

    }
}

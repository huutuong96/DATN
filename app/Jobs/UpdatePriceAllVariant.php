<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\product_variants;
class UpdatePriceAllVariant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $variant_ids;
    protected $price;
    public function __construct($variant_ids, $price)
    {
        $this->variant_ids = $variant_ids;
        $this->price = $price;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $variants = product_variants::whereIn('id', $this->variant_ids)
            ->update(['price' => $this->price]);
    }
}

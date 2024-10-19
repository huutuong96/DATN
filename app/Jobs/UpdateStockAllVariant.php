<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\product_variants;
class UpdateStockAllVariant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $variant_ids;
    protected $stocks;
    public function __construct($variant_ids, $stocks)
    {
        $this->variant_ids = $variant_ids;
        $this->stocks = $stocks;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $variants = product_variants::whereIn('id', $this->variant_ids)
            ->update(['stock' => $this->stocks]);
    }
}

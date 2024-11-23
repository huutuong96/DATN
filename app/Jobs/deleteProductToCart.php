<?php

namespace App\Jobs;

use App\Models\ProducttocartModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class deleteProductToCart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $carts;
    public function __construct($carts)
    {
        $this->carts = $carts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ProducttocartModel::whereIn('id', $this->carts)->delete();
    }
}

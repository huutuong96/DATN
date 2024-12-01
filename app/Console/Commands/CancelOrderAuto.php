<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CancelOrderAuto extends Command
{
    /**
     * Tên và mô tả của command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-auto';

    protected $description = 'Automatically cancel orders every minute.';

    /**
     * Thực thi command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // cái này Tỏ vô rút tơ này
            Http::get(route('cancel_order_auto'));
            $this->info('Successfully executed cancel_order_auto.');
        } catch (\Exception $e) {
            $this->error('Error executing cancel_order_auto: ' . $e->getMessage());
        }

        return 0;
    }
}

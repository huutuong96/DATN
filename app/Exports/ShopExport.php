<?php

namespace App\Exports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\FromCollection;

class ShopExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Shop::select('id','shop_name','location','ward','district','province','cccd','owner_id','revenue','wallet','account_number','bank_name','owner_bank')->get();
    }
}

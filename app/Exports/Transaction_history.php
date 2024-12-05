<?php

namespace App\Exports;

use App\Models\history_get_cash_shops;
use Maatwebsite\Excel\Concerns\FromCollection;

class Transaction_history implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        
        return history_get_cash_shops::where('shop_id', $this->request->shop_id)->get();
    }
}

<?php

namespace App\Exports;

use App\Models\OrdersModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrderExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        return OrdersModel::where('shop_id', $this->request->shop_id)->get();
    }
}

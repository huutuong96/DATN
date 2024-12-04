<?php

namespace App\Exports;

use App\Models\OrdersModel;
use Maatwebsite\Excel\Concerns\FromCollection;


class OrderDetailExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        $orders = OrdersModel::with(['orderDetails.variant.product', 'shop','payment','timeline']) // Eager load 'product' qua 'orderDetails'
            ->where('id', $this->request->order_id)
            ->get();
            foreach ($orders as $order) {
                foreach ($order->orderDetails as $orderDetail) {
                    if($orderDetail->variant!=null){
                        $variant = $orderDetail->variant;  
                    }else{
                        $product = $orderDetail->product;  
                    }
                  
                }
            }
            
            foreach ($orders as $key => $order) {
                foreach ($order->orderDetails as $orderDetail  ) {
                    if( $orderDetail->variant){
                       $orderDetail['product']  = $orderDetail->variant->product;
                       unset($orderDetail->variant['product']);
                    }
                }
            }
        return $orders;
    }
}

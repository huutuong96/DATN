<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Tax;
use App\Models\OrdersModel;
use App\Models\order_fee_details;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VnshopController extends Controller
{
    public function login()
    {
        return view('login');
    }
    public function dashboard()
    {
        $checkShop = Shop::where("status", 3)
        ->get()
        ->count();
        $checkProduct = Product::where("status", 3)
                                ->where('is_delete', 0)
                                ->get()
                                ->count();

        $monthlyRevenue = order_fee_details::
        whereMonth('created_at', Carbon::now()->month)
        ->sum('amount');
        $tax_vnshop = Tax::where("type" , 'san')
                           ->sum("rate");

        // $monthlyRevenue = $monthlyRevenue ;
        $monthlyRevenueOrder = OrdersModel::where("status", 2)
        ->whereMonth('created_at', Carbon::now()->month)
        ->get();
        
        $doanhthu = array_fill(1, Carbon::now()->day, 0);
        foreach ($monthlyRevenueOrder as $order) {
            $day = $order->created_at->day; 
            if ($day <= Carbon::now()->day) { 
                $doanhthu[$day] += $order->total_amount; 
            }else{
                brack();
            }
        }
        $doanhthuJson = array_values($doanhthu);

        $feedBack;
        return view('dashboard.dashboard',compact(
            'checkProduct',
            'checkShop',
            'monthlyRevenue',
            'doanhthuJson'
        ));
    }
    public function store()
    {
        return view('stores.list_store');
    }
}

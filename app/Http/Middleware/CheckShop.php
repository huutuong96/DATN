<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Learning_sellerModel;
use App\Models\Shop_manager;
use App\Models\Shop;
class CheckShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // LẤY ID CỦA SHOP TỪ TẤT CẢ CÁC NGUỒN
        $shopId = $request->route('shop_id');
        // KIỂM TRA XEM SHOP CÓ HOÀN THÀNH KHÓA HỌC CHO NHÀ BÁN HÀNG KHÔNG
        $shop_learning = Learning_sellerModel::where('shop_id', $shopId)->first();
        if($shop_learning->status == 101){
            return response()->json([
                'message' => 'Bạn chưa hoàn thành khóa học dành cho nhà bán hàng',
            ], 403);
        }

        // KIỂM TRA XEM CÓ PHẢI LÀ NHÂN VIÊN HOẶC THÀNH VIÊN CỦA SHOP HAY KHÔNG
        $shop_manager = Shop_manager::where('user_id', auth()->user()->id)->first();
        if($shop_manager){
            return $next($request);
        }
        return response()->json([
            'message' => 'Bạn không có quyền truy cập shop này',
        ], 403);
    }
}

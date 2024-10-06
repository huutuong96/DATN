<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Learning_sellerModel;
use App\Models\Shop_manager;
use App\Models\Shop;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $userId = JWTAuth::parseToken()->authenticate()->id;
        // LẤY ID CỦA SHOP TỪ TẤT CẢ CÁC NGUỒN
        $shopId = $request->input('shop_id')
            ?? $request->route('shop_id')
            ?? $request->query('shop_id')
            ?? $request->segment(2) // Assuming shop_id might be in the second segment of the URL
            ?? $request->header('X-Shop-ID') // In case it's passed as a custom header
            ?? $request->json('shop_id'); // For JSON payloads
        // dd(vars: $shopId);
        // KIỂM TRA XEM SHOP CÓ HOÀN THÀNH KHÓA HỌC CHO NHÀ BÁN HÀNG KHÔNG
        $shop_learning = Learning_sellerModel::where('shop_id', $shopId)->first();
        if ($shop_learning && $shop_learning->status == 101) {
            return response()->json([
                'message' => 'Bạn chưa hoàn thành khóa học dành cho nhà bán hàng',
            ], 403);
        }
        // KIỂM TRA XEM CÓ PHẢI LÀ NHÂN VIÊN HOẶC THÀNH VIÊN CỦA SHOP HAY KHÔNG
        $shop_manager = Shop_manager::where('user_id', $userId)->first();

        if($shop_manager){
            return $next($request);
        }
        return response()->json([
            'message' => 'Bạn không có quyền truy cập shop này',
        ], 403);
    }
}

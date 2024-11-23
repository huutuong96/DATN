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
        $userId = JWTAuth::parseToken()->authenticate();
        // CHECK XEM SHOP NÀY ĐÃ CÓ vnp_TmnCode CỦA VNPAY CHƯA NẾU CHƯA THÌ CHUYỂN VỀ TRANG THÊM MÃ VNPAY
        // $shopId = $request->id;
        // $shop = Shop::find($shopId);
        // if ($shop->vnp_TmnCode == null) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'CỬA HÀNG CHƯA KHAI BÁO MÃ TÀI KHOẢN NGÂN HÀNG CỦA VNPAY',
        //     ], 400);
        // }
            if ($userId->role_id == 2 || $userId->role_id == 3 || $userId->role_id == 4) {
                return $next($request);
            }
            
        
    }
}

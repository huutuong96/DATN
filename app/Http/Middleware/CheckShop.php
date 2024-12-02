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
        // $shopId = $request->id;
        $shop = Shop::where('owner_id', $userId->id)->select('id', 'shopid_GHN')->first();
        if ($shop->shopid_GHN == null) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng cập nhật thiết lập đơn vị vận chuyển',
                'url' => 'https://vnshop.top/register/shipping/view'
            ], 408);
        }
            if ($userId->role_id == 2 || $userId->role_id == 3 || $userId->role_id == 4) {
                return $next($request);
            }
            
        
    }
}

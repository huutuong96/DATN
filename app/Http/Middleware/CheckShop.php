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
        // LẤY ID CỦA SHOP TỪ TẤT CẢ CÁC NGUỒN
        $shopId = $request->input('shop_id')
            ?? $request->route('shop_id')
            ?? $request->query('shop_id')
            ?? $request->segment(2) // Assuming shop_id might be in the second segment of the URL
            ?? $request->header('X-Shop-ID') // In case it's passed as a custom header
            ?? $request->json('shop_id'); // For JSON payloads
            // dd($userId);
            if ($userId->role_id == 2 || $userId->role_id == 3 || $userId->role_id == 4) {
                return $next($request);
            }
            
        
    }
}

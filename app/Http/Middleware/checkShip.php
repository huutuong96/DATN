<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkShip
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shopId = $request->shop_id;
        $shop = Shop::where('id', $shopId)->select('id', 'shopid_GHN')->first();
        if ($shop->shopid_GHN == null) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng cập nhật thiết lập đơn vị vận chuyển',
                'url' => 'https://vnshop.top/register/shipping/view'
            ], 408);
        }
        return $next($request);
    }
}

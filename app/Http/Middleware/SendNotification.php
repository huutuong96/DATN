<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Shop;
use App\Models\ProgramtoshopModel;
use App\Models\Programme_detail;
use App\Models\Product;
use App\Models\Shop_manager;
use App\Http\Controllers\NotificationController;

class SendNotification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shopId = $request->route('id');
        // dd()
        if ($shopId && $request->method() !== 'POST') {
            $shop = Shop::find($shopId);
            if (!$shop) {
                return abort(404, 'Shop không tồn tại.');
            }
        }

        // $this->check_quantity_product_to_shop($shopId);
        return $next($request);
    }



    private function check_quantity_product_to_shop(string $id)
    {
        $products = Product::where('shop_id', $id)
                ->where('quantity', '<', 10)
                ->orWhere('quantity', 0)
                ->get();
        $owner_id = Shop_manager::where('shop_id', $id)->where('role', 'owner')->value('user_id');
        foreach ($products as $product) {
            if ($product->quantity == 0) {
                $notificationData = [
                    'type' => 'main',
                    'user_id' => $owner_id,
                    'title' => $product->name . ' đã hết hàng',
                    'description' => $product->name . ' đã hết hàng, Bạn có thể thêm sản phẩm để bán.',
                ];
                $notificationController = new NotificationController();
                $notification = $notificationController->store(new Request($notificationData));
            }
            if ($product->quantity < 10) {
                $notificationData = [
                    'type' => 'main',
                    'user_id' => $owner_id,
                    'title' => $product->name . ' số lượng còn lại ít',
                    'description' => $product->name . ' số lượng còn lại ít, Bạn có thể thêm sản phẩm để bán.',
                ];
                $notificationController = new NotificationController();
                $notification = $notificationController->store(new Request($notificationData));
            }
        }
    }


}

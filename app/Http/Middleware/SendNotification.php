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
use Illuminate\Support\Facades\Cache;

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

        if ($shopId && $request->method() !== 'POST') {
            $shop = Shop::find($shopId);
            if (!$shop) {
                return abort(404, 'Shop không tồn tại.');
            }
            $this->executeEveryOtherDay($shopId);
        }

        return $next($request);
    }


    private function executeEveryOtherDay(string $shopId)
    {
        $cacheKey = 'last_execution_' . $shopId;
        $lastExecution = Cache::get($cacheKey);

        if (!$lastExecution || now()->diffInDays($lastExecution) >= 2) {
            $this->programe_to_shop_the_end($shopId);
            $this->check_quantity_product_to_shop($shopId);

            Cache::put($cacheKey, now(), 60 * 60 * 24 * 2); // Cache for 2 days
        }
    }

    private function programe_to_shop_the_end(string $id)
    {
        $programs = ProgramtoshopModel::where('shop_id', $id)->get();
        $updatedPrograms = [];
        $owner_id = Shop_manager::where('shop_id', $id)->where('role', 'owner')->value('user_id');
        foreach ($programs as $program) {
            $program_detail = Programme_detail::find($program->program_id);
            if ($program_detail) {
                $endDate = $program_detail->created_at->addDays(5);
                if (now()->greaterThan($endDate) && $program_detail->status != 102) {
                    $program_detail->status = 102;
                    $program_detail->save();
                    $updatedPrograms[] = $program_detail;
                }
            }
            $notificationData = [
                'type' => 'main',
                'user_id' => $owner_id,
                'title' => $program_detail->title . ' đã kết thúc',
                'description' => $program_detail->title . ' đã kết thúc, Bạn có thể gia hạn chương trình để tiếp tục sử dụng.',
            ];
            $notificationController = new NotificationController();
            $notification = $notificationController->store(new Request($notificationData));
        }
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

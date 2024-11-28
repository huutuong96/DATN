<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\history_get_cash_shops;
use App\Models\User;
use App\Models\UsersModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientEmbedController extends Controller
{
    public function wallet(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $shop = Shop::where('id', $request->shop_id)->first();
        return view('client.wallet' , compact('shop','user'));
    }

    public function updateBank(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->first();
        $shop->update([
            'account_number' => $request->account_number ?? null,
            'bank_name' => $request->bank_name ?? null,
            'owner_bank' => $request->owner_bank ?? null,
        ]);
        return redirect()->back()->with('success', 'Cập nhật thông tin ngân hàng thành công');
    }

    public function shop_request_get_cash(Request $request)
    {
    //    $user = $request->user;
    //    $user = UsersModel::where('email', $user)->select('id')->first();
    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Token không đúng",
    //         ], 401);
    //     }
        $shop = Shop::where('id', $request->shop_id)->first();
        // if (!$user) {
        //     return redirect()->back()->with('error', 'Vui lòng đăng nhập');
        // }
        // if ($shop->wallet < $request->get_cash) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Số dư trong ví không đủ",
        //     ], 401);
        // }
        // $cash = $shop->wallet - $request->get_cash;
        $shop->update([
            'wallet' => 0,
        ]);
        
        history_get_cash_shops::create([
            'shop_id' => $shop->id ?? null,
            'user_id' =>  $user ?? null,
            'cash' => $shop->wallet ?? null,
            'date' => Carbon::now() ?? null,
            'account_number' => $shop->account_number ?? null,
            'bank_name' => $shop->bank_name ?? null,
            'owner_bank' => $shop->owner_bank ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Yêu cầu rút tiền thành công",
        ], 200);
        // return redirect()->back()->with('success', 'Yêu cầu rút tiền thành công');
    }

    public function register_shipping(Request $request)
    {
        $user = UsersModel::where('email', $request->email)->select('id', 'fullname', 'phone')->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Tài khoản không tồn tại');
        }
        $shop = Shop::where('owner_id', $user->id)->select('id', 'shopid_GHN', 'province_id', 'district_id', 'ward_id', 'location')->first();
        if (!$shop) {
            return redirect()->back()->with('error', 'Shop không tồn tại');
        }
        if ($shop->shopid_GHN != null) {
            return redirect()->back()->with('error', 'Shop đã đăng ký đơn vị vận chuyển');
        }
        if ($shop->province_id == null || $shop->district_id == null || $shop->ward_id == null) {
            return redirect()->back()->with('error', 'Vui lòng cập nhật địa chỉ shop');
        }
        $response = Http::withHeaders([
            'Token' => env('TOKEN_API_GIAO_HANG_NHANH_DEV'),
            'Content-Type' => 'application/json',
        ])->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shop/register', [
            'district_id' => (int) $shop->district_id,
            'ward_code' =>  $shop->ward_id,
            'address' => $shop->location,
            'name' => $user->fullname ?? $request->fullname,
            'phone' =>  $user->phone ?? $request->phone,
        ]);
        $shop->update([
            'shopid_GHN' => $response->json()['data']['shop_id'] ?? null
        ]);
        return view('shipping.register_shipping_success');
        // return redirect()->back()->with('success', 'Cập nhật mã shop GHN thành công');
    }    

    public function register_shipping_view(Request $request)
    {
       return view('shipping.registerGHN');
    }    
}

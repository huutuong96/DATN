<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\history_get_cash_shops;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
       $user = $request->user;
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Token không đúng",
            ], 401);
        }
        $shop = Shop::where('id', $request->shop_id)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Vui lòng đăng nhập');
        }
        if ($shop->wallet < $request->get_cash) {
            return response()->json([
                'status' => false,
                'message' => "Số dư trong ví không đủ",
            ], 401);
        }
        $cash = $shop->wallet - $request->get_cash;
        $shop->update([
            'wallet' => $cash ?? $shop->wallet,
        ]);
        history_get_cash_shops::create([
            'shop_id' => $shop->id ?? null,
            'user_id' =>  $user ?? null,
            'cash' => $request->get_cash ?? null,
            'date' => Carbon::now() ?? null,
            'account_number' => $shop->account_number ?? null,
            'bank_name' => $shop->bank_name ?? null,
            'owner_bank' => $shop->owner_bank ?? null,
        ]);
        return redirect()->back()->with('success', 'Yêu cầu rút tiền thành công');
    }
    public function product_update(Request $request)
    {
        // $user = JWTAuth::parseToken()->authenticate();
        // $shop = Shop::where('id', $request->shop_id)->first();
        // return view('client.product_update' , compact('shop','user'));
        return view('client.product_update');
    }
}

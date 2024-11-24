<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientEmbedController extends Controller
{
    public function wallet(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->first();
        return view('client.wallet' , compact('shop'));
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
}

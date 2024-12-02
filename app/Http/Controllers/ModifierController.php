<?php

namespace App\Http\Controllers;

use App\Models\modifiers;
use App\Models\subdomains;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ModifierController extends Controller
{
    public function subdomain()
    {
        $subdomains = subdomains::where('status', 1)->get();
        return view('modifier.list_modifier', compact('subdomains'));
    }

    public function create_subdomain(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $token = $request->token;
        $insertData = [
            'sub_domain' => $request->sub_domain.".vnshop.top",
            'main_domain' => $request->main_domain ?? "vnshop.top",
            'descriptions' => $request->descriptions,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'status' => $request->status,
            // 'icon_sub' => $request->icon_sub,
        ];
        subdomains::create($insertData);
        return redirect()->route('subdomain', [
            'token' => $token,
        ])->with('message', 'Thêm mới subdomain thành công');
    }   

    public function delete_subdomain(Request $request)
    {
        $token = $request->token;
        $subdomain = subdomains::find($request->id);
        $subdomain->delete();
        return redirect()->route('subdomain', [
            'token' => $token,
        ])->with('message', 'Xóa subdomain thành công');
    }

    public function modifiers()
    {
        $modifiers = modifiers::all();
        return view('modifier.modifier', compact('modifiers'));
    }

    public function page_ctkm()
    {
        return view('modifier.ctkm');
    }

}

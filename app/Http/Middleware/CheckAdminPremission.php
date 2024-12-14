<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\role_premissionModel;

class CheckAdminPremission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $premission): Response
    {
        $user = JWTAuth::parseToken()->authenticate();
        // dd($user);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản không tồn tại',
            ], 401);
        };
        if($user->role_id == 2){
            return $next($request);
        }
        $premissions = role_premissionModel::with('permission')
        ->where('role_id', $user->role_id)
        ->get();
        foreach ($premissions as $key => $value) {
            if($value->permission->premissionName == $premission){
                // dd($value->permission->premissionName, $premission);
                return $next($request);
            }
        }return Back()->with('error', 'Bạn không được cấp quyền để sử dụng chức năng này!');
        
    }
}

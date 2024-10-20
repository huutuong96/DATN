<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản không tồn tại',
            ], 401);
        }
        $role = DB::table('roles')->where('id', $user->role_id)->first();
        
        if ($role->title == 'OWNER') {
            return $next($request);
        }
        if ($role->title == $role) {
            return $next($request);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Bạn không có quyền vào trang này',
        ], 401);

    }
}

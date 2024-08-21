<?php

namespace App\Http\Middleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản không tồn tại',
            ], 401);
        }
        $per_id = DB::table('role_premissions')->where('role_id', $user->role_id)->get();
        // dd($per_id);
        if ($per_id) {
            foreach ($per_id as $permission) {
                if ( $permission->premission_id == 1 && $request->isMethod('get')) {
                    return $next($request);
                }
                if ( $permission->premission_id == 2 && $request->isMethod('post')) {
                    return $next($request);
                }
                if ( $permission->premission_id == 3 && $request->isMethod('put')) {
                    return $next($request);
                }
                if ( $permission->premission_id == 4 && $request->isMethod('delete')) {
                    return $next($request);
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền truy cập chức năng này.',
            ], 403);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'bạn không có quyền truy cập chức năng này.',
            ], 401);
        }

    }
}

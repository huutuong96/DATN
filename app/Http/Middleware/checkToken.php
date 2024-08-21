<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class checkToken
{
    public function handle($request, Closure $next)
    {
        // Lấy token từ header
        try {
            // Xác thực người dùng bằng token JWT
            $user = JWTAuth::parseToken()->authenticate();
            $token = DB::table('users')->where('refesh_token', $user->refesh_token)->first();
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token không hợp lệ. Vui lòng đăng nhập lại.',
                ], 401);
            }
            return $next($request);
        } catch (TokenExpiredException $e) {
            // Token đã hết hạn
            return response()->json([
                'status' => 'error',
                'message' => 'Token đã hết hạn, vui lòng đăng nhập lại',
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc không tồn tại',
                'error' => $e->getMessage(),
            ], 401); // Sử dụng 401 cho lỗi xác thực
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }


    }
}

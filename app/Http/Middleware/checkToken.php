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
        if ($request->token) {
            try {
                    $user = JWTAuth::parseToken()->authenticate();
                    return $next($request);
            } catch (TokenExpiredException $e) {
                return redirect()->route('login');

            } catch (JWTException $e) {
                return redirect()->route('login');

            } catch (\Exception $e) {
                return redirect()->route('login');
            }
        }
        try {
            // Xác thực người dùng bằng token JWT
            $user = JWTAuth::parseToken()->authenticate();
            // Lấy token hiện tại từ request
            $currentToken = JWTAuth::getToken();

            // Kiểm tra xem token hiện tại có khớp với token được lưu trong database không
            $storedToken = DB::table('users')->where('id', $user->id)->value('refesh_token');
            if (!$storedToken || $currentToken != $storedToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phiên đăng nhập không hợp lệ hoặc đã hết hạn. Vui lòng đăng nhập lại.',
                ], 401);
            }
            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token đã hết hạn, vui lòng đăng nhập lại',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc không tồn tại',
                'error' => $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

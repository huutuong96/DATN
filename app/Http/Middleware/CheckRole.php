<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
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
        if ($request->token) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                // dd($user);
                if ($user->role_id == 2 || $user->role_id == 3) {
                    return $next($request);
                }
            } catch (TokenExpiredException $e) {
                return redirect()->route('login')->with('message', 'Bạn không có quyền vào trang này');

            } catch (JWTException $e) {
                return redirect()->route('login')->with('message', 'Bạn không có quyền vào trang này');

            } catch (\Exception $e) {
                return redirect()->route('login')->with('message', 'Bạn không có quyền vào trang này');
            }
        }
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Chưa đăng nhập',
            ], 401);
        }
        $role = DB::table('roles')->where('id', $user->role_id)->first();
        
        if ($role->title == 'OWNER' || $role->title == 'MANAGER') {
            return $next($request);
        }
       
        return response()->json([
            'status' => 'error',
            'message' => 'Bạn không có quyền vào trang này',
        ], 401);

    }
}

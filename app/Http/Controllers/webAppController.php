<?php

namespace App\Http\Controllers;

use App\Models\CategoriesModel;
use App\Models\Image;
use App\Models\log_delete;
use App\Models\Product;
use App\Models\product_variants;
use App\Models\tax_category;
use App\Models\web_app;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tymon\JWTAuth\Facades\JWTAuth;

class webAppController extends Controller
{
    public function index()
    {
        $apps = web_app::all();
        return view('webapp.web_app', ['apps' => $apps]);
    }

    public function create(Request $request)
    {
        $token = $request->query('token');
        if ($request->icon_app) {
            $urlIcon = $this->storeImage($request->icon_app);
        }
        $app = new web_app();
        $app->name = $request->name_app ?? 'Không tên';
        $app->icon = $urlIcon ?? null;
        $app->url = $request->url_app ?? null;
        $app->save();
        return redirect()->route('list_app', [
            'token' => $token,
        ])->with('message', 'Thêm web app thành công');
    }

    public function delete_app(Request $request)
    {
        $token = $request->query('token');
        $id = $request->query('id');
        web_app::where('id', $id)->delete();
        return redirect()->route('list_app', [
            'token' => $token,
        ])->with('message', 'Xóa web app thành công');
    }

    private function storeImage($image)
    {
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        return $uploadedImage['secure_url'];
    }
    
    public function setting_admin(Request $request)
    {
        return view('setting_admin.setting_admin');
    }

    // public function delete_all_categories(Request $request)
    // {
    //     DB::beginTransaction();
    //     $credentials = $request->only('email', 'password');
    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác');
    //     }
    //     $user = JWTAuth::parseToken()->authenticate();
        
    //     log_delete::create([
    //         'user' => $user->fullname ?? null,
    //         'do_action' => 'XÓA TẤT CẢ DỮ LIỆU',
    //         'code' => 'CATEGORIES',
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);
    //     DB::commit();
    //     return redirect()->route('setting_admin', [
    //         'token' => $token,
    //     ])->with('message', 'Xóa tất cả web app thành công');
    // }

    // public function delete_all(Request $request)
    // {
    //     DB::beginTransaction();
    //     $credentials = $request->only('email', 'password');
    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác');
    //     }
    //     $user = JWTAuth::parseToken()->authenticate();
        
    //     log_delete::create([
    //         'user' => $user->fullname ?? null,
    //         'do_action' => 'XÓA TẤT CẢ DỮ LIỆU',
    //         'code' => 'CATEGORIES',
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);
    //     DB::commit();
    //     return redirect()->route('setting_admin', [
    //         'token' => $token,
    //     ])->with('message', 'Xóa tất cả web app thành công');
    // }
public function delete_all(Request $request)
{
    $credentials = $request->only('email', 'password');
    if (!$token = JWTAuth::attempt($credentials)) {
        return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác');
    }
    $user = JWTAuth::parseToken()->authenticate();
    log_delete::create([
        'user' => $user->fullname ?? null,
        'do_action' => 'XÓA TẤT CẢ DỮ LIỆU',
        'code' => 'ALL_DATA',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
        // Xóa tất cả dữ liệu từ tất cả các bảng
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tablesToExclude = ['roles','ranks','premissions','role_premissions','taxs','users','web_apps','payments','log_deletes','jobs','failed_jobs','history_get_cash_shops','platform_fees','config_main','cart_to_users'];
        foreach (DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            $tableName = $table_array[key($table_array)];
            if (!in_array($tableName, $tablesToExclude)) {
                DB::table($tableName)->truncate();
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        // Commit giao dịch
        return redirect()->route('setting_admin', [
            'token' => $token,
        ])->with('message', 'Xóa tất cả dữ liệu thành công');
   
}

// public function delete_data(Request $request){
//     $credentials = $request->only('email', 'password');
//     if (!$token = JWTAuth::attempt($credentials)) {
//         return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác');
//     }
//     $user = JWTAuth::parseToken()->authenticate();
//     log_delete::create([
//         'user' => $user->fullname ?? null,
//         'do_action' => 'XÓA TẤT CẢ DỮ LIỆU',
//         'code' => 'ALL_DATA',
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);
//         // Xóa tất cả dữ liệu từ tất cả các bảng
//         DB::statement('SET FOREIGN_KEY_CHECKS=0');
//         $tablesToExclude = ['roles','ranks','premissions','role_premissions','taxs','users','web_apps','payments','log_deletes','jobs','failed_jobs','history_get_cash_shops','platform_fees','config_main','cart_to_users'];
//         foreach (DB::select('SHOW TABLES') as $table) {
//             $table_array = get_object_vars($table);
//             $tableName = $table_array[key($table_array)];
//             if (!in_array($tableName, $tablesToExclude)) {
//                 DB::table($tableName)->truncate();
//             }
//         }
//         DB::statement('SET FOREIGN_KEY_CHECKS=1');
//         // Commit giao dịch
//         return redirect()->route('setting_admin', [
//             'token' => $token,
//         ])->with('message', 'Xóa tất cả dữ liệu thành công');
// }
public function delete_data_by_date(Request $request)
{
    $credentials = $request->only('email', 'password');
    if (!$token = JWTAuth::attempt($credentials)) {
        return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác');
    }
    $user = JWTAuth::parseToken()->authenticate();
    log_delete::create([
        'user' => $user->fullname ?? null,
        'do_action' => 'XÓA DỮ LIỆU THEO NGÀY',
        'code' => 'DATA_BY_DATE',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    $tablesToExclude = ['roles','ranks','premissions','role_premissions','taxs','users','web_apps','payments','log_deletes','jobs','failed_jobs','history_get_cash_shops','platform_fees','config_main','cart_to_users'];
    foreach (DB::select('SHOW TABLES') as $table) {
        $table_array = get_object_vars($table);
        $tableName = $table_array[key($table_array)];
        if (!in_array($tableName, $tablesToExclude)) {
            if (Schema::hasColumn($tableName, 'created_at')) {
                DB::table($tableName)->whereBetween('created_at', [$startDate, $endDate])->whereNotNull('created_at')->delete();
            }
        }
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    return redirect()->route('setting_admin', [
        'token' => $token,
    ])->with('message', 'Xóa dữ liệu từ ngày ' . $startDate . ' đến ngày ' . $endDate . ' thành công');
}

   
}

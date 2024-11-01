<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Tax;
use App\Models\Blog;
use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\order_fee_details;
use App\Models\CategoriesModel;
use App\Models\role_permissionModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VnshopController extends Controller
{
    public function __construct() {
        $this->middleware('checkRole')->only('list_role', 'list_permission');

    }
    public function login()
    {
        return view('login');
    }
    public function dashboard()
    {
        $checkShop = Shop::where("status", 3)
        ->get()
        ->count();
        $shopAC = Shop::where("status", 1)
        ->get()
        ->count();
        $checkProduct = Product::where("status", 3)
                                ->where('is_delete', 0)
                                ->get()
                                ->count();

        $monthlyRevenue = order_fee_details::
        whereMonth('created_at', Carbon::now()->month)
        ->sum('amount');
        $tax_vnshop = Tax::where("type" , 'san')
                           ->sum("rate");

        // $monthlyRevenue = $monthlyRevenue ;
        $monthlyRevenueOrder = OrdersModel::whereMonth('created_at', Carbon::now()->month)
        ->get();
        
        $doanhthu = array_fill(1, Carbon::now()->day, 0);
        $luongtrahang = array_fill(1, Carbon::now()->day, 0);
        $luotmua = array_fill(1, Carbon::now()->day, 0);
        foreach ($monthlyRevenueOrder as $order) {
            $day = $order->created_at->day; 
            if ($day <= Carbon::now()->day) { 

                $doanhthu[$day] += ($order->total_amount / 100000 );
                if($order->status == 5){
                    $luongtrahang[$day] += 1;
                }
                $luotmua[$day] += 1;
                 
            }else{
                break;
            }
        }
        $doanhthuJson = array_values($doanhthu);
        $luongtrahangJson = array_values($luongtrahang);
        $luotmuaJson = array_values($luotmua);
        $listCategory =( CategoriesModel::where("parent_id", null)->get());

        $listCategoryJson = array_column($listCategory->toArray(), 'title');
        
        $listCategoryID = array_column($listCategory->toArray(), 'id');
        // $categoryId = 1;
        $listCategorydoanhthu = array_fill(1, count($listCategoryJson) -1, 0);
        foreach ($listCategoryID as $key => $id) {
            $totalRevenue = $this->calculateSubtotalByCategory($id);
            // dd($totalRevenue);
            $listCategorydoanhthu[$key] = $totalRevenue;
        }
        // dd($listCategorydoanhthu);
        $listCategorydoanhthu = array_values($listCategorydoanhthu);
        $colors = [
            'red',
            'green',
            'blue',
            'yellow',
            'orange',
            'purple',
            'cyan',
            'magenta',
            'black',
            'white',
            'gray',
            'brown',
            'pink',
            'lime',
            'navy',
            'teal',
            'violet'
        ];
        $listCategoryColors= array_slice($colors, 0, count($listCategoryJson));
        $listShopId = array_unique(array_column($monthlyRevenueOrder->toArray(), 'shop_id'));
        $listShop = [];
        foreach ($listShopId as $idKey => $shopId) {
            $doanhthu = 0;
            $shop = Shop::where("id", $shopId)->with('user')->first();
            foreach ($monthlyRevenueOrder as $orderKey => $order) {
                if($order->status == 2){
                    if($order->shop_id == $shopId){
                        $doanhthu += $order->net_amount;
                    }
                }
                
            }
            $shop["doanhthu"] = $doanhthu ;
            $listShop[] = $shop;
        }
        // dd($listShop);
        usort($listShop, function($a, $b) {
            return $b->doanhthu <=> $a->doanhthu;
        });

        $feedBack;
        return view('dashboard.dashboard',compact(
            'checkProduct',
            'checkShop',
            'monthlyRevenue',
            'doanhthuJson',
            'luongtrahangJson',
            'luotmuaJson',
            'listCategoryJson',
            'listCategorydoanhthu',
            'listCategoryColors',
            'listShop',
            'shopAC'

        ));
    }
    public function store($limit = 5)
    {    $shops = Shop::whereIn("status", [1, 2])->with('user')->paginate($limit);
         foreach ($shops as $Key => $shop) {
            $doanhthu = OrdersModel::whereMonth('created_at', Carbon::now()->month)
                                    ->where('shop_id', $shop->id)->sum('net_amount');
            $shop["doanhthu"] = $doanhthu;
    
         }
        return view('stores.list_store',compact(
            'shops'
        ));
    }
    public function productWaitingApproval()
    {
        return view('products.list_product');
    }
    function getAllCategoryIds($categoryId) {
        // Lấy tất cả danh mục con của $categoryId
        $categories = CategoriesModel::where('parent_id', $categoryId)->get();
    
        $categoryIds = [$categoryId]; // Bắt đầu từ ID của danh mục cha
    
        foreach ($categories as $category) {
            // Đệ quy để lấy danh mục con
            $categoryIds = array_merge($categoryIds, $this->getAllCategoryIds($category->id));
        }
    
        return $categoryIds;
    }
    function calculateSubtotalByCategory($categoryId) {
        // Lấy tất cả ID của danh mục (bao gồm cả danh mục cha và con)
        $categoryIds = $this->getAllCategoryIds($categoryId);
    
        // Tính tổng subtotal của tất cả sản phẩm trong các danh mục đã lấy
        $totalSubtotal = OrderDetailsModel::whereIn('category_id', $categoryIds)->sum('subtotal');
    
        return $totalSubtotal;
    }
    public function list_category($limit = 5){
        $categories = CategoriesModel::orderBy('created_at', 'desc')->whereIn("status", [1, 2])->paginate($limit);
        return view('categories.list_category',compact(
            'categories'
        ));
    }
    public function trash_category($limit = 5){
        $categories = CategoriesModel::orderBy('updated_at', 'desc')->where('status', "=", 5 )->paginate($limit);
        return view('categories.trash',compact(
            'categories'
        ));
    }
    public function trash_stores($limit = 5){
        $shops = Shop::orderBy('updated_at', 'desc')->where('status', "=", 5 )->paginate($limit);
        return view('stores.trash',compact(
            'shops'
        ));
    }
    public function violation_stores($limit = 5){
        $shops = Shop::orderBy('updated_at', 'desc')->where('status', "=", 4 )->paginate($limit);
        return view('stores.violation',compact(
            'shops'
        ));
    }
    public function pending_approval_stores($limit = 5){
        $shops = Shop::orderBy('updated_at', 'desc')->whereIn("status", [101, 3])->paginate($limit);
        return view('stores.pending_approval',compact(
            'shops'
        ));
    }
    public function changeCategory(Request $rqt){
       
        $category = CategoriesModel::find($rqt->id);
        if ($category) {
            $category->status =$rqt->status; 
            $category->save(); 
            return Back();
        }
    }
    public function changeShop(Request $rqt){
       
        $shop = Shop::find($rqt->id);
        if ($shop) {
            $shop->status =$rqt->status; 
            $shop->save(); 
            return Back();
        }
    }
    public function blog($limit = 5){
        $blogs = Blogs::orderBy('created_at', 'desc')->where('is_delete', "!=", 0)->where('is_delete', "!=", 5)->paginate($limit);
        return view('blogs.blogs',compact(
            'blogs'
        ));
    }
    public function costomer($limit = 5){
        $customer_id = RolesModel::where('title', 'CUSTOMER')->first('id');
        // dd($customer_id->id);
        $users = UsersModel::orderBy('created_at', 'desc')->with('address')->with('rank')->whereIn("status", [1, 2])->where('role_id', $customer_id->id)->paginate($limit);
        // dd($users);
        return view('users.list_customer',compact(
            'users'
        ));
    }
    public function manager($limit = 5){
        $customer_id = RolesModel::where('title', '!=', 'CUSTOMER')->where('title', '!=', 'OWNER')->pluck('id');
        // dd($customer_id);
        $users = UsersModel::orderBy('created_at', 'desc')->with('address')->with('rank')->whereIn("status", [1, 2])->whereIn('role_id', $customer_id)->paginate($limit);
        $roles = RolesModel::all();
        // dd($roles[0]->title);
        // dd($users);
        return view('users.list_manager',compact(
            'users',
            'roles'
        ));
    }
    public function changeUser(Request $rqt){
       
        $user = UsersModel::find($rqt->id);
        if ($user) {
            $user->status =$rqt->status; 
            $user->save(); 
            return Back();
        }
    }
    public function trashUser($limit = 5){
        $users = UsersModel::orderBy('updated_at', 'desc')->with('address')->with('rank')->where('status', "=", 5 )->paginate($limit);
        return view('users.trash',compact(
            'users'
        ));
    }
    public function pendingApproval($limit = 5){
        $users = UsersModel::orderBy('updated_at', 'desc')->with('address')->with('rank')->whereIn("status", [101, 3])->paginate($limit);
        return view('users.pending_approval',compact(
            'users'
        ));
    }
    public function list_role(Request $request){
        $limit = $request->limit ?? 10;
        $roles = RolesModel::orderBy('created_at', 'desc')->paginate($limit);
        return view('roles.list_role',compact(
            'roles'
        ));
    }

    public function list_permission(Request $request){
        $permission = role_permissionModel::where('role_id', $request->id)->where('status', 2)->first();
        dd($permission);
        return view('roles.list_permission',compact(
            'permission'
        ));
    }
    
}

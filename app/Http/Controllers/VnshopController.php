<?php

namespace App\Http\Controllers;
use Cloudinary\Cloudinary;
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
use App\Models\role_premissionModel;
use App\Models\PremissionsModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\voucherToMain;
use App\Http\Requests\VoucherRequest;
use Illuminate\Support\Str;
use App\Http\Requests\Blogrequest;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Http\Requests\TaxRequest;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Models\tax_category;
use App\Http\Requests\CategoriesRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\categoryattribute;
use App\Models\Message;
use App\Models\message_detail;
use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use App\Models\RanksModel;
use App\Models\recipes;
use App\Http\Requests\RankRequest;
use App\Models\PaymentsModel;
use App\Http\Requests\PaymentRequest;

use App\Models\Image;
use App\Http\Requests\ProductRequest;
use App\Jobs\check_time_event;
use App\Jobs\check_time_event_hand;
use App\Jobs\checkWebDie;
use App\Jobs\EventMail;

;
use App\Models\ColorsModel;
use App\Models\variantattribute;
use App\Models\attributevalue;
use App\Models\product_variants;
use App\Models\Attribute;

use App\Services\ImageUploadService;
use App\Jobs\UploadImageJob;
use App\Jobs\UploadImagesJob;
use App\Jobs\UpdateStockAllVariant;
use App\Jobs\UpdatePriceAllVariant;
use App\Jobs\UpdateImageAllVariant;

use App\Models\update_product;
use App\Models\Event;
use App\Models\ProducttocartModel;
use GuzzleHttp\Client;

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
        $shopAC = Shop::where("status", 2)
        ->get()
        ->count();
        $product_rejecte = Product::where("status", 3)
                               
                                ->get()
                                ->count();
      $allUpdateProductsCount = update_product::all()->count();
       $checkProduct=$product_rejecte+$allUpdateProductsCount;
        $monthlyRevenue = order_fee_details::
        whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->sum('amount');
        // dd($monthlyRevenue);
        $tax_vnshop = Tax::where("type" , 'san')
                           ->sum("rate");

        // $monthlyRevenue = $monthlyRevenue ;
        $monthlyRevenueOrder = OrdersModel::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->get();
        
        $doanhthu = array_fill(1, Carbon::now()->day, 0);
        $luongtrahang = array_fill(1, Carbon::now()->day, 0);
        $luotmua = array_fill(1, Carbon::now()->day, 0);
        foreach ($monthlyRevenueOrder as $order) {
            $day = $order->created_at->day; 
            if ($day <= Carbon::now()->day) { 
                if($order->status == 2){
                    $doanhthu[$day] += ($order->total_amount );
                    if($order->status == 9){
                        $luongtrahang[$day] += 1;
                    }
                    $luotmua[$day] += 1;
                    
                }
                 
            }else{
                break;
            }
        }
        $doanhthuJson = array_values($doanhthu);
        $luongtrahangJson = array_values($luongtrahang);
        $luotmuaJson = array_values($luotmua);
        $listCategory =( CategoriesModel::where("parent_id", 0)->get());

        $listCategoryJson = array_column($listCategory->toArray(), 'title');
        
        $listCategoryID = array_column($listCategory->toArray(), 'id');
        // $categoryId = 1;
        // $listCategorydoanhthu = array_fill(1, count($listCategoryJson) -1, 0);
        $listCategorydoanhthu = array_fill(1, max(count($listCategoryJson) - 1, 1), 0);
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
    //  $listCategorydoanhthu = [1,2,3,4,5,6,6];  
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
            'shopAC'

        ));
    }
    public function store($limit = 5)
    {    $shops = Shop::whereIn("status", [1, 2, 4])->with('user')->orderBy("created_at", "Desc")->paginate($limit);
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
        $categories = CategoriesModel::where('parent_id', $categoryId)->get();
    
        $categoryIds = [$categoryId]; 
        foreach ($categories as $category) {
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
    public function list_category($limit = 5)
    {
        $categories = CategoriesModel::orderBy('created_at', 'desc')
        ->whereIn("status", [1,2])
        ->get();  
        $category = CategoriesModel::orderBy('created_at', 'desc')
        ->whereIn("status", [2])
        ->get();  
        $categoryTree = $this->buildTree($category);

        $taxes = Tax::where('status', 2)->get();
        $tax_category = tax_category::all();
    
        return view('categories.list_category', compact(
            'categoryTree', 'taxes', 'tax_category', 'categories',
        ));
    }


    /**
     * Hàm đệ quy xây dựng cấu trúc cây danh mục
     */
    private function buildTree($categories, $parentId = 0)
    {
        $tree = [];
    
        foreach ($categories as $category) {
           
            if ($category->parent_id == $parentId) { 
                $children = $this->buildTree($categories, $category->id);
                if ($children->isNotEmpty()) {
                    $category->children = $children;
                }
                $tree[] = $category;
            }
        }
    
        return collect($tree);
    }
    
    
    
    public function trash_category($limit = 5){
        $categories = CategoriesModel::orderBy('updated_at', 'desc')->where('status', "=", 5 )->paginate($limit);
        $ListCategories = CategoriesModel::all();
        return view('categories.trash',compact(
            'categories',
            'ListCategories'
        ));
    }
    
    public function storeCategory(CategoriesRequest $request, $limit = 5){
        $dataInsert = [];

        // Kiểm tra và upload ảnh
        if ($request->file('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $dataInsert['image'] = $cloudinary->uploadApi()->upload($image->getRealPath())['secure_url'];
        }

        $user = JWTAuth::parseToken()->authenticate();

        try {
            $dataInsert = [
                'title' => $request->title,
                'slug' => $request->slug ?? Str::slug($request->title, '-'),
                'index' => $request->index ?? 1,
                'status' => $request->status ?? 1,
                'parent_id' => $request->parent_id2,
                'create_by' => $user->id,
                'image' => $dataInsert['image'] ?? null,
                'tax_id' => $request->tax_id
            ];

            $category = CategoriesModel::create($dataInsert);
            $tax_category = tax_category::create([
                'category_id' => $category->id,
                'tax_id' => $request->tax_id,
            ]);
            $parentId = $category->parent_id;
            while ($parentId) {
                $parentAttributes = CategoryAttribute::where('category_id', $parentId)->get();

                foreach ($parentAttributes as $parentAttribute) {
                    $parentAttribute->status = 0;
                    $parentAttribute->save();
                }

                $parentCategory = CategoriesModel::find($parentId);
                $parentId = $parentCategory ? $parentCategory->parent_id : null;
            }
            $subCategories = CategoriesModel::where('parent_id', $category->id)->get();
            if ($subCategories->isEmpty()) {
                $attributeIds = $request->attribute_ids;
                if (is_array($attributeIds) && count($attributeIds) > 0) {
                    foreach ($attributeIds as $attributeId) {
                        categoryattribute::insert([
                            'category_id' => $category->id,
                            'attribute_id' => $attributeId,
                        ]);
                    }
                }
            }
            if($request->back == 1){
                // session()->put('message', 'Tạo thành công!');
                return redirect()->route('list_category', ['token' => auth()->user()->refesh_token])->with('message', 'Tạo thành công!');
                // return back()->with('message', 'Tạo thành công!');
            }
            return response()->json([
                'status' => true,
                'message' => "Thêm danh mục thành công",
                'data' => $category,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm danh mục không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    
    public function trash_stores($limit = 5){
        $shops = Shop::orderBy('updated_at', 'desc')->where('status', "=", 5 )->paginate($limit);
        return view('stores.trash',compact(
            'shops'
        ));
    }
    public function violation_stores($limit = 5){
        $shops = Shop::orderBy('updated_at', 'desc')->where('status', "=", 4 )->paginate($limit);
        return view('stores.Violation',compact(
            'shops'
        ));
    }
    public function pending_approval_stores($limit = 5){
        $shops = Shop::orderBy('updated_at', 'desc')->whereIn("status", [101, 3])->paginate($limit);
        return view('stores.pending_approval',compact(
            'shops'
        ));
    }
    // public function changeCategory(Request $rqt){
       
    //     $category = CategoriesModel::find($rqt->id);
    //     // if($rqt->status == ){

    //     // }
    //     $chillrenCategory = CategoriesModel::where("parent_id", $rqt->id)->where("status", 2)->get();
    //     if ($category) {
    //         if($chillrenCategory){
    //             return Back()->with('message', 'Cập nhật không thành công vì có danh mục con đang hoạt động!');
    //         }else{
    //             $category->status =$rqt->status; 
    //             $category->save(); 
    //             return Back()->with('message', 'Cập nhật thành công!');
    //         }
           
    //     }
    //     return Back()->with('message', 'Không có sản phẩm nào!');
    // }



    // public function changeCategory(Request $rqt)
    // {  
    //     $category = CategoriesModel::find($rqt->id);
       
    //     if (!$category) {
    //         return Back()->with('message', 'Không tìm thấy danh mục!');
    //     } 
    //     if ($rqt->status == 1 || $rqt->status == 5) {
    //         $chillrenCategory = CategoriesModel::where("parent_id", $rqt->id)
    //                                             ->where("status", 2) 
    //                                             ->get();
    //         if ($chillrenCategory) {
    //             return Back()->with('error', 'Không thể xóa danh mục cha vì có danh mục con đang hoạt động!');
    //         }
    //     }
    //     $category->status = $rqt->status;  
    //     $category->save(); 
    
    //     return Back()->with('message', 'Cập nhật thành công!');
    // }
    

    public function changeCategory(Request $rqt)
    {  
        $category = CategoriesModel::find($rqt->id);
       
        if (!$category) {
            return Back()->with('message', 'Không tìm thấy danh mục!');
        } 
        if ($rqt->status == 1 || $rqt->status == 5) {
           // Kiểm tra trước khi xóa
            if ($this->hasActiveDescendants($rqt->id)) {
                return back()->with('error', 'Không thể xóa danh mục cha vì có danh mục con, cháu hoặc chắt đang hoạt động!');
            }
        }
        $category->status = $rqt->status;  
        $category->save(); 
    
        return Back()->with('message', 'Cập nhật thành công!');
    }

    function hasActiveDescendants($categoryId) {
        // Lấy danh sách danh mục con
        $children = CategoriesModel::where('parent_id', $categoryId)->where('status', 2)->get();
    
        foreach ($children as $child) {
            // Kiểm tra nếu danh mục con đang hoạt động
            if ($child->status == 2) {
                return true;
            }
    
            // Đệ quy kiểm tra danh mục cháu, chắt
            if (hasActiveDescendants($child->id)) {
                return true;
            }
        }
    
        return false;
    }
    
    
    
    
    
    public function updateCategory(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        // dd($request);
        try {
            $categories = CategoriesModel::find($request->id);

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Danh mục không tồn tại",
                ], 404);
            }
            if ($request->file('image')) {
                $image = $request->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $imageUrl = $uploadedImage['secure_url'];
            }

            $dataUpdate = [
                'title' => $request->title ?? $categories->title,
                'slug' => Str::slug($request->title),
                'index' => $request->index ?? $categories->index,
                'image' => $imageUrl ?? $categories->image,
                'status' => $request->status ?? $categories->status,
                'tax_id' => $request->tax_id,
                'parent_id' => $request->parent_id ?? $categories->parent_id,
                'update_by' => $user->id,
                'updated_at' => now(),
            ];
            $categories->update($dataUpdate);
            $tax_category = tax_category::create([
                'category_id' => $categories->id,
                'tax_id' => $request->tax_id,
            ]);
            return redirect()->route('list_category', ['token' => auth()->user()->refesh_token])->with('message', 'Cập nhật thành công!');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật danh mục không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
        
    }
    public function changeShop(Request $rqt){
       
        $shop = Shop::find($rqt->id);
        $user_id = $shop->owner_id;
        if ($shop) {
            $shop->status =$rqt->status; 
            $shop->save(); 
            switch ($rqt->status) {
                case "1":
                    $notificationRequest = new Request([
                        'type' => 'main',
                        'user_id' => $user_id,
                        'title' => 'Tài khoản của bạn bị khóa',
                        'image' => "https://down-vn.img.susercontent.com/file/sg-11134004-7rfgy-m3q0hdin6yea8c_tn",
                        'description' => 'Tài khoản '.$shop->shop_name.' đã bị khóa',
                        'shop_id' => $shop->id 
                    ]);
                    $notificationController = new NotificationController();
                    $notificationController->store($notificationRequest);
                    break;
                case "4":
                    $notificationRequest = new Request([
                        'type' => 'main',
                        'user_id' => $user_id,
                        'title' => 'Tài khoản của bạn đã vi phạm',
                        'image' => "https://down-vn.img.susercontent.com/file/sg-11134004-7rfgy-m3q0hdin6yea8c_tn",
                        'description' => 'Tài khoản ' .$shop->shop_name.' đã bị vi phạm',
                        'shop_id' => $shop->id 
                    ]);
                    $notificationController = new NotificationController();
                    $notificationController->store($notificationRequest);
                    break;
                case "5":
                    $notificationRequest = new Request([
                        'type' => 'main',
                        'user_id' => $user_id,
                        'title' => 'Tài khoản của bạn bị xóa',
                        'image' => "https://down-vn.img.susercontent.com/file/sg-11134004-7rfgy-m3q0hdin6yea8c_tn",
                        'description' => 'Tài khoản '.$shop->shop_name.' đã bị xóa',
                        'shop_id' => $shop->id 
                    ]);
                    $notificationController = new NotificationController();
                    $notificationController->store($notificationRequest);
                    break;
               
            }
            return back()->with('message', 'Cập nhật thành công!');
        }
    }
    public function changeUserSearch(Request $rqt){
       
        $user = UsersModel::find($rqt->id);
        if ($user) {
            $user->status =$rqt->status; 
            $user->save();  
            // dd($rqt->tab);
            // dd(session('tab'));
            session()->forget('tab');
            // dd(session('tab'));
            session()->put('tab', $rqt->tab);
            // dd(session('tab'));
            return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $rqt->tab,'search'=>$rqt->search])->with('message', 'Cập nhật thành công!');
        }
    }
    public function blog(Request $request){
        $tab = $request->input('tab', 1); 
        
        $blogs = Blog::whereNull('deleted_at')->get();
        $deletedBlog = Blog::onlyTrashed()->get();
        return view('blogs.blogs',compact(
            'blogs','deletedBlog','tab'
        ));
    }
    public function post(Request $request)
    {
        $tab = $request->input('tab', 1); 
        $Posts = Post::whereNull('deleted_at')
                    ->with('blog')
                    ->orderBy('created_at', 'desc') 
                    ->get();
    
        $blogs = Blog::whereNull('deleted_at')->get();
        $deletedPost = Post::onlyTrashed()->get();
    
        return view('blogs.posts', compact('Posts', 'blogs', 'deletedPost', 'tab'));
    }
    
    public function restorepost(Request $request, $id)
    {

       
        $tab = $request->query('tab');
        $token = $request->query('token');
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore(); 
        // return $tab;
        return redirect()->route('post',['token' => $token, 'tab' => $tab])->with('message', 'post đã được khôi phục.');
}

    public function updatepost(PostRequest $request, string $id)
            {
                $token = $request->query('token');
                $tab = $request->query('tab');
                $post = Post::where('deleted_at', null)->findOrFail($id);
            
                $post->title = $request->title;
                $post->slug = Str::slug($request->title, '-');
                $post->updated_by = auth()->user()->id;
                $post->updated_at = now();
                $post->content = $request->content;
                $post->blog_id = $request->blog_id;
            
                if ($request->hasFile('image')) {
                    $imagePath =  $this->storeImage($request->image);
                    $post->image = $imagePath;
                }
            
                $post->save();

                return  back()->with('message', 'Đã cập nhật');
            }

                
        public function updateBlog(Blogrequest $request, string $id)
        {
            $token = $request->query('token');
            $tab = $request->query('tab');
            $blog = Blog::where('id', $id)->whereNull('deleted_at')->firstOrFail();
            $slug = Str::slug($request->name, '-');
            $blog->name = $request->name;
            $blog->title = $request->title;
            $blog->slug = $slug; 
            $blog->updated_by = auth()->user()->id; 
            $blog->save();

            return redirect()->route('blog', [
                'token' => $token,
                'tab' => $tab
            ])->with('message', 'Đã cập nhật');
        }

        public function updatevoucher(VoucherRequest $request, $id)
        {
            $token = $request->token;
            $tab = $request->tab;
        
            $voucherMain = voucherToMain::where('id', $id)->firstOrFail();
        
            
            $voucherMain->title = $request->title ?? $voucherMain->title; 
            $voucherMain->description = $request->description ?? $voucherMain->description;
            $voucherMain->quantity = $request->quantity ?? $voucherMain->quantity;
            $voucherMain->limitValue = $request->limitValue ?? $voucherMain->limitValue;
            $voucherMain->min = $request->min_order ?? $voucherMain->min;
            $voucherMain->ratio = $request->ratio ?? $voucherMain->ratio;
            $voucherMain->code = $request->code ?? $voucherMain->code;
            $voucherMain->status = $request->status ?? $voucherMain->status;
            $voucherMain->update_by = auth()->user()->id;
            $voucherMain->save();
            return redirect()->route('voucherall', [
                'token' => $token,
                'tab'=>$tab,
            ])->with('message', 'Cập nhật voucher main thành công!');
        }
        public function delete_voucher(request $request, $id)
        {
            $token = $request->token;
            $tab = $request->tab;
            $voucherMain = voucherToMain::where('id', $id)->firstOrFail();
            $voucherMain->delete();
            return redirect()->route('voucherall', [
                'token' => $token,
                'tab' => $tab,
            ])->with('message', 'Xóa voucher main thành công!');
        }
        
        
      
        

        public function restoreBlog(Request $request, $id)
        {
            $tab = $request->query('tab');
            $token = $request->query('token');
            $blog = Blog::onlyTrashed()->findOrFail($id);
            $blog->restore(); 

            return redirect()->route('blog',['token' => $token, 'tab' => $tab])->with('message', 'Blog đã được khôi phục.');
}
            

 
    public function costomer($limit = 5){
        $customer_id = RolesModel::where('title', 'CUSTOMER')->first('id');
        // dd($customer_id->id);
        $users = UsersModel::orderBy('created_at', 'desc')->with('address')->with('role')->with('rank')->whereIn("status", [1, 2])->where('role_id', $customer_id->id)->paginate($limit);
        // dd($users);
        return view('users.list_customer',compact(
            'users'
        ));
    }
    public function manager($limit = 5){
        $customer_id = RolesModel::where('title', '!=', 'CUSTOMER')->pluck('id');
        // ->where('title', '!=', 'OWNER')
        // dd($customer_id);
        $users = UsersModel::orderBy('created_at', 'desc')->with('address')->with('rank')->whereIn("status", [1,2,4])->whereIn('role_id', $customer_id)->paginate($limit);
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
            switch ($rqt->status) {
                case "2":
                    $notificationRequest = new Request([
                        'type' => 'main',
                        'user_id' => $user->id,
                        'title' => 'Tài khoản của bạn bị khóa',
                        'description' => 'Tài khoản '.$user->fullname.' đã bị khóa',
                        'shop_id' => $user->shop->id 
                    ]);
                    $notificationController = new NotificationController();
                    $notificationController->store($notificationRequest);
                    break;
                case "4":
                    $notificationRequest = new Request([
                        'type' => 'main',
                        'user_id' => $user->id,
                        'title' => 'Tài khoản của bạn đã vi phạm',
                        'description' => 'Tài khoản ' .$user->fullname.' đã bị vi phạm',
                       
                    ]);
                    $notificationController = new NotificationController();
                    $notificationController->store($notificationRequest);
                    break;
                case "5":
                    $notificationRequest = new Request([
                        'type' => 'main',
                        'user_id' => $user->id,
                        'title' => 'Tài khoản của bạn bị xóa',
                        'description' => 'Tài khoản '.$user->fullname.' đã bị xóa',
                        
                    ]);
                    $notificationController = new NotificationController();
                    $notificationController->store($notificationRequest);
                    break;
               
            }
            
            return Back();
        }
    }
    function userChangeRole(Request $rqt) {
        $user = UsersModel::findOrFail( $rqt->id);
        $user->role_id = $rqt->roleId;
        $user->save();
        return back()->with('success', 'Cập nhật chức vụ thành công!');
    }

    // public function changeUser(Request $rqt){
    //     $user = UsersModel::find($rqt->id);
    //     if ($user) {
    //         $user->status = $rqt->status;
    //         $user->save();
    //         $notifications = [
    //             "2" => [
    //                 'title' => 'Tài khoản của bạn bị khóa',
    //                 'description' => 'Tài khoản ' . $user->fullname . ' đã bị khóa',
    //             ],
    //             "4" => [
    //                 'title' => 'Tài khoản của bạn đã vi phạm',
    //                 'description' => 'Tài khoản ' . $user->fullname . ' đã vi phạm',
    //             ],
    //             "5" => [
    //                 'title' => 'Tài khoản của bạn bị xóa',
    //                 'description' => 'Tài khoản ' . $user->fullname . ' đã bị xóa',
    //             ],
    //         ];
    
    //         if (isset($notifications[$rqt->status])) {
    //             $notificationRequest = new Request([
    //                 'type' => 'main',
    //                 'user_id' => $user->id,
    //                 'title' => $notifications[$rqt->status]['title'],
    //                 'description' => $notifications[$rqt->status]['description'],
    //             ]);
    
    //             $notificationController = new NotificationController();
    //             $notificationController->store($notificationRequest);
    //         }
    
    //         return back();
    //     }
    // }
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
        $permissions = PremissionsModel::all();
        $role = RolesModel::where('id', $request->id)->first();
        $role_premission = role_premissionModel::where('role_id', $request->id)->get();
        if (!$role_premission) {
            $role_premission = [];
        }
        // dd($role_premission);
        return view('roles.list_permission',compact(
            'permissions', 'role', 'role_premission'
        ));
    }
    
    public function search(Request $rqt)  {
    
        $db = [
            "products" => ["name", "sku", "slug", "description"],
            "shops" => ["shop_name", "slug", "description"],
            "users" => ["fullname", "phone", "email", "description"],
            "posts" => ["slug", "title", "content"]
        ];
    
        $search = $rqt->search;
        $perPage = $rqt->input('per_page', 10); // Default records per page
        $resultsByTable = [];
    
        foreach ($db as $table => $columns) {
            $tableResults = collect();
    
            foreach ($columns as $column) {
                $query = DB::table($table)->where($column, 'like', "%$search%");
    
                if ($table == 'products') {
                    // Pagination for products
                    $results = $query->get();
                    $resultsByTable[$table] = $results;
                    break; // Stop once products are paginated
                } elseif ($table == 'shops') {
                    // Separate handling for shops with eager loading using Eloquent
                    $results = \App\Models\Shop::with('user')
                        ->where($column, 'like', "%$search%")
                        ->get();
                    $resultsByTable[$table] = $results;
                    break; // Stop once shops are paginated
                } elseif ($table == 'users') {
                    // Separate handling for shops with eager loading using Eloquent
                    $results = \App\Models\UsersModel::with('address')
                        ->where($column, 'like', "%$search%")
                        ->get();
                    $resultsByTable[$table] = $results;
                    break; // Stop once shops are paginated
                } elseif ($table == 'posts') {
                    // Separate handling for shops with eager loading using Eloquent
                    $results = \App\Models\Post::with('user')
                        ->where($column, 'like', "%$search%")
                        ->get();
                    $resultsByTable[$table] = $results;
                    break; // Stop once shops are paginated
                }   else {
                    // No pagination for other tables
                    $results = $query->get();
                    $tableResults = $tableResults->merge($results);
                }
            }
    
            if (!isset($resultsByTable[$table])) {
                $resultsByTable[$table] = $tableResults;
            }
        }
    
        session()->put('tab', $rqt->tab ?? 'products');
        // dd("têst".session('tab'));
        return view('search.search', compact('resultsByTable', 'search'));
    }

    private function storeImage($image)
    {
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        return $uploadedImage['secure_url'];
    }

    public function taxall(request $request)
{    $tab = $request->query('tab',1);
    // dd($tab);
    $taxes = Tax::where('status',2)->orderBy('created_at', 'desc')->get();
    $taxeOFF = Tax::where('status',3)->orderBy('updated_at', 'desc')->get();

    if ($taxes->isEmpty()) {
        return view('tax.tax')->with('message', 'Không tồn tại thuế nào');
    }

    return view('tax.tax', compact('taxes' ,'taxeOFF', 'tab'));
}
public function storetax(TaxRequest $request)
{
    $token = $request->query('token');
    $tab = $request->query('tab');
   
    $tax = new Tax();
    $tax->title = $request->title;
    $tax->type = $request->type;
    $tax->tax_number = $request->tax_number;
    $tax->rate = $request->rate;
    $tax->status = $request->status;
    $tax->create_by = auth()->user()->id; 
    $tax->save();
    return redirect()->route('taxall', [
        'token' => $token,
        
    ])->with('success', 'Thêm thuế thành công');
    
}


public function update_tax(TaxRequest $request, $id)
{
    $token = $request->token; 
    $tab = $request->tab; 
    $tax = Tax::findOrFail($id);
    $tax->title = $request->title ?? $tax->title; 
    $tax->type = $request->type ?? $tax->type;
    $tax->tax_number = $request->tax_number ?? $tax->tax_number;
    $tax->rate = $request->rate ?? $tax->rate;
    $tax->status = $request->status ?? $tax->status;
    $tax->update_by = auth()->user()->id;
    $tax->save();
    return redirect()->route('taxall', [
        'token' => $token,
        'tab' => $tab,
    ])->with('message', 'Cập nhật thuế thành công!');
}
public function destroytax(Request $request, string $id)
{
    try {
        $token = $request->token; 
        $tab = $request->tab; 
        $tax = Tax::findOrFail($id);
        $tax->delete();
        return redirect()->route('taxall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Xóa thuế thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('taxall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Xóa thuế không thành công!');
    }
}
public function changeStatusTax(Request $request, string $id)
{
    try {
        $token = $request->token;
        $tab = $request->tab;
        $tax = Tax::findOrFail($id);
        if (\DB::table('tax_category')->where('tax_id', $tax->id)->exists()) {
            return redirect()->route('taxall', [
                'token' => $token,
                'tab' => $tab,
            ])->with('message', 'Không thể thay đổi trạng thái vì thuế đang được áp dụng cho danh mục!');
        }
        $tax->status = $request->status;
        $tax->save();
        return redirect()->route('taxall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Thay đổi trạng thái thuế thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('taxall', [
            'token' => $request->token,
            'tab' => $request->tab,
        ])->with('message', 'Thay đổi trạng thái thuế không thành công!');
    }
}


public function bannerall(Request $request)
{
    $tab = $request->input('tab', 1); 
    $banners = Banner::where('status',2)->orderBy('created_at', 'desc')->paginate(10);
    $banners0ff = Banner::where('status',3)->orderBy('created_at', 'desc')->paginate(10);

   

    return view('banner.banner', compact('banners', 'banners0ff', 'tab'));
}
public function storebanner(BannerRequest $request)
{
    $image = $request->file('image');
    $cloudinary = new Cloudinary();
    $token = $request->query('token');
   
    try {
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        
        $dataInsert = [
            'title' => $request->title,
            'content' => $request->content,
            'image' => $uploadedImage['secure_url'], 
            'URL' => $request->URL,
            'status' => $request->status,
            'index' => $request->index,
            'create_by' => auth()->user()->id,
        ];
        
        $banner = Banner::create($dataInsert);

        return redirect()->route('bannerall', [
            'token' => $token,
        ])->with('success', 'Thêm banner thành công');
    } catch (\Throwable $th) {
        return redirect()->route('bannerall', [
            'token' => $token,
        ])->with('error', 'Thêm banner thất bại: ' . $th->getMessage());
    }
}

public function updatebanner(BannerRequest $request, $id)
{
    $token = $request->token; 
    $tab = $request->tab;
    $banner = Banner::findOrFail($id);
    $dataUpdate = [
        'title' => $request->title,
        'content' => $request->content,
        'status' => $request->status,
        'URL' => Str::limit($request->URL, 2083), 
        'index' => $request->index,
        'update_by' => auth()->user()->id,
    ];

    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $dataUpdate['image'] = $uploadedImage['secure_url']; 
        }
        $banner->update($dataUpdate);
        return redirect()->route('bannerall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Cập nhật banner thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('bannerall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('error', 'Cập nhật banner thất bại: ' . $th->getMessage());
    }
}

public function statistByQuantity(Request $request)
{
    // 1. Tính số lượng sản phẩm bán ra theo ngày trong tháng hiện tại
    $monthlyRevenueOrder = OrdersModel::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->where('status', 2) // Chỉ lấy đơn hàng hoàn thành
        ->get();

    $soluong = array_fill(1, Carbon::now()->day, 0); // Khởi tạo mảng với giá trị 0
    $tong = 0;

    foreach ($monthlyRevenueOrder as $order) {
        $day = $order->created_at->day;

        // Tổng số lượng sản phẩm trong ngày tương ứng
        $dailyQuantity = OrderDetailsModel::where('order_id', $order->id)
            ->whereDay('created_at', $day)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('quantity');

        $soluong[$day] += $dailyQuantity;
        $tong += $dailyQuantity;
    }

    $soluongJson = array_values($soluong); // Mảng số lượng sản phẩm theo ngày

    // 2. Tính số lượng sản phẩm bán ra theo từng tháng trong năm hiện tại
    $soluongnam = array_fill(1, 12, 0); // Mỗi phần tử đại diện cho một tháng

    $monthlyRevenueYear = OrderDetailsModel::whereHas('order', function ($query) {
        $query->whereYear('created_at', Carbon::now()->year)
              ->where('status', 2); // Chỉ lấy đơn hàng hoàn thành
    })->get();

    foreach ($monthlyRevenueYear as $orderDetail) {
        $month = $orderDetail->created_at->month;
        $soluongnam[$month] += $orderDetail->quantity; // Cộng số lượng vào tháng tương ứng
    }

    $soluongnamJson = array_values($soluongnam); // Mảng số lượng theo tháng

    // 3. Tính số lượng sản phẩm bán ra theo từng năm trong 5 năm gần đây
    $currentYear = Carbon::now()->year;
    $soluongcacnam = [];
    $soluongcacnamJson = [];

    for ($i = 4; $i >= 0; $i--) {
        $year = $currentYear - $i;

        $yearQuantity = OrderDetailsModel::whereHas('order', function ($query) use ($year) {
            $query->whereYear('created_at', $year)
                  ->where('status', 2); // Chỉ lấy đơn hàng hoàn thành
        })->sum('quantity');

        $soluongcacnam[$year] = $yearQuantity;
    }

    $soluongcacnamJson = array_values($soluongcacnam); // Mảng số lượng theo năm

    // 4. Lấy danh sách các cửa hàng và số lượng sản phẩm bán ra
    $listShopId = array_unique(array_column($monthlyRevenueOrder->toArray(), 'shop_id'));
    $listShop = [];

    foreach ($listShopId as $shopId) {
        $shop = Shop::where('id', $shopId)->with(relations: 'user')->first();
        $shopQuantity = OrderDetailsModel::where('shop_id', $shopId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereHas('order', function ($query) {
                $query->where('status', 2); // Điều kiện status của bảng orders
            })
            ->sum('quantity');

        $shop['soluong'] = $shopQuantity; // Gắn thêm trường số lượng vào thông tin cửa hàng
        $listShop[] = $shop;
    }
    // Sắp xếp các cửa hàng theo số lượng bán ra (giảm dần)
    usort($listShop, function ($a, $b) {
        return $b['soluong'] <=> $a['soluong'];
    });
    
    // 5. Trả về view với dữ liệu đã tính toán
    return view('statist.quantity_sold', compact(
        'soluongJson',
        'soluongnamJson',
        'soluongcacnamJson',
        'listShop',
        'tong'
    ));
}

public function statistByRevenue(Request $request)
{   
    // Lấy doanh thu theo ngày trong tháng hiện tại
    $monthlyRevenueOrder = OrdersModel::whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year) // Thêm điều kiện cho năm
    ->get();

    // Mảng doanh thu cho từng ngày trong tháng
    $doanhthu = array_fill(1, Carbon::now()->day, 0);

    // Lặp qua tất cả các đơn hàng trong tháng này
    foreach ($monthlyRevenueOrder as $order) {
    $day = $order->created_at->day;
    // Chỉ tính doanh thu của các ngày nhỏ hơn hoặc bằng ngày hiện tại
    if ($day <= Carbon::now()->day) { 
        if ($order->status == 2) { // Kiểm tra nếu đơn hàng có trạng thái đã hoàn thành (status == 2)
            $doanhthu[$day] += $order->total_amount; // Cộng doanh thu vào ngày tương ứng
        }
    }
    }

    // Chuyển mảng doanh thu thành mảng giá trị mà không có chỉ mục
    $doanhthuJson = array_values($doanhthu);

    // Doanh thu năm hiện tại (tính tổng doanh thu theo tháng)
    $doanhthunam = array_fill(1, Carbon::now()->month, 0);

    // Lặp qua các đơn hàng trong năm này và tính doanh thu theo tháng
    $monthlyRevenueYear = OrdersModel::whereYear('created_at', Carbon::now()->year)
    ->where('status', 2) // Lọc chỉ những đơn hàng đã hoàn thành
    ->get();

    // Tính doanh thu cho từng tháng trong năm hiện tại
    foreach ($monthlyRevenueYear as $order) {
    $month = $order->created_at->month;
    $doanhthunam[$month] += $order->total_amount; // Cộng doanh thu vào tháng tương ứng
    }

    // Chuyển mảng doanh thu thành mảng giá trị cho doanh thu theo tháng
    $doanhthunamJson = array_values($doanhthunam);

    // Dữ liệu doanh thu của các năm gần đây
    // $doanhthucacnam = ['2020', '2021', '2022', '2023', '2024'];
    $currentYear = Carbon::now()->year;

    // Tạo mảng 5 năm gần đây
    $doanhthucacnam = [];
    for ($i = 4; $i >= 0; $i--) {
        $doanhthucacnam[] = $currentYear - $i;
    }

    $doanhthucacnamJson = [];

    foreach ($doanhthucacnam as $year) {
    // Tính tổng doanh thu của năm đã chọn
    $yearRevenue = OrdersModel::whereYear('created_at', $year)
        ->where('status', 2)
        ->sum('total_amount');
    $doanhthucacnamJson[$year] = $yearRevenue; // Lưu doanh thu của năm vào mảng
    }
    $doanhthucacnamJson1 = array_values($doanhthucacnamJson);

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

        usort($listShop, function($a, $b) {
            return $b->doanhthu <=> $a->doanhthu;
        });

        return view('statist.revenue',compact(  'doanhthuJson',
                                                'doanhthunamJson',
                                                'doanhthucacnamJson1',
                                                'listShop'
                                             )
                    );
    }
    public function statistBySales(Request $request)
    {
    $TongSoLuongBanRa = OrdersModel::whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->count();
    $DangGiao = OrdersModel::whereIn("order_status", [4, 5])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();
    $DoiTra = OrdersModel::whereIn("order_status", [9])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();
    $Huy = OrdersModel::whereIn("order_status", [10])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();
    $HoanThanh = OrdersModel::whereIn("order_status", [7,8])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();
    $ThatBai = OrdersModel::whereIn("order_status", [6])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();
    $ChoDuyet = OrdersModel::whereIn("order_status", [0,1,2,3])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();
    $ChuaThanhToan = OrdersModel::whereIn("order_status", [11])->whereMonth('created_at', Carbon::now()->month)
    ->whereYear('created_at', Carbon::now()->year)
    ->where('status', 2)->count();

    $monthlyRevenueOrder = OrdersModel::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->where('status', 2) // Chỉ lấy đơn hàng hoàn thành
        ->get();
    //-------------
    $luongtrahang = array_fill(1, Carbon::now()->day, 0);
    $luotmua = array_fill(1, Carbon::now()->day, 0);
    $bihuy = array_fill(1, Carbon::now()->day, 0);
    $loi = array_fill(1, Carbon::now()->day, 0);
    //-------------
    $luongtrahangnam = array_fill(1, 12, 0);
    $luotmuanam = array_fill(1, 12, 0);
    $bihuynam = array_fill(1, 12, 0);
    $loinam = array_fill(1, 12, 0);

    // Lấy dữ liệu đơn hàng trong năm hiện tại
    $ordersCurrentYear = OrdersModel::whereYear('created_at', Carbon::now()->year)->where('status', 2)->get();

    // Xử lý dữ liệu theo từng tháng trong năm
    foreach ($ordersCurrentYear as $order) {
        $month = $order->created_at->month; // Tháng tạo đơn hàng
        $luotmuanam[$month] += 1; // Mỗi đơn hàng tăng lượt mua
        switch ($order->order_status) {
            case 9: // Đơn hàng trả hàng
                $luongtrahangnam[$month] += 1;
                break;
            case 10: // Đơn hàng bị hủy
                $bihuynam[$month] += 1;
                break;
            case 6: // Đơn hàng lỗi
                $loinam[$month] += 1;
                break;
        }
    }

    // Chuyển đổi mảng thành JSON cho thống kê theo tháng
    $luongtrahangThangJson = array_values($luongtrahangnam);
    $luotmuaThangJson = array_values($luotmuanam);
    $bihuyThangJson = array_values($bihuynam);
    $loiThangJson = array_values($loinam);

    //-------------
    $currentYear = Carbon::now()->year;
    $yearsRange = range($currentYear - 4, $currentYear); // 5 năm gần nhất
    $luongtrahangcacnam = array_fill_keys($yearsRange, 0);
    $luotmuacacnam = array_fill_keys($yearsRange, 0);
    $bihuycacnam = array_fill_keys($yearsRange, 0);
    $loicacnam = array_fill_keys($yearsRange, 0);

    // Lấy dữ liệu đơn hàng của 5 năm gần nhất
    $ordersLastFiveYears = OrdersModel::whereYear('created_at', '>=', $currentYear - 4)->where('status', 2)
    ->get();

    // Xử lý dữ liệu theo năm
    foreach ($ordersLastFiveYears as $order) {
    $year = $order->created_at->year; // Năm tạo đơn hàng
    if (in_array($year, $yearsRange)) {
        $luotmuacacnam[$year] += 1; // Mỗi đơn hàng tăng lượt mua
        switch ($order->order_status) {
            case 10: // Đơn hàng trả hàng
                $luongtrahangcacnam[$year] += 1;
                break;
            case 9: // Đơn hàng bị hủy
                $bihuycacnam[$year] += 1;
                break;
            case 6: // Đơn hàng lỗi
                $loicacnam[$year] += 1;
                break;
        }
    }
    }

    // Chuyển đổi mảng thành JSON cho thống kê theo năm
    $luongtrahangCacNamJson = array_values($luongtrahangcacnam);
    $luotmuaCacNamJson = array_values($luotmuacacnam);
    $bihuyCacNamJson = array_values($bihuycacnam);
    $loiCacNamJson = array_values($loicacnam);

    //---------------------------------

    foreach ($monthlyRevenueOrder as $order) {
        $day = $order->created_at->day; 
        if ($day <= Carbon::now()->day) { 
            if($order->status == 2){
                if($order->order_status == 10){
                    $luongtrahang[$day] += 1;
                }
                if($order->order_status == 9){
                    $bihuy[$day] += 1;
                }
                if($order->order_status == 6){
                    $loi[$day] += 1;
                }
                $luotmua[$day] += 1;
            }
                
        }else{
            break;
        }
    }
    $luongtrahangJson = array_values($luongtrahang);
    $luotmuaJson = array_values($luotmua);
    $bihuyJson = array_values($bihuy);
    $loiJson = array_values($loi);

    $listShopId = array_unique(array_column($monthlyRevenueOrder->toArray(), 'shop_id'));
    $listShop = [];
    foreach ($listShopId as $idKey => $shopId) {
        $shop = Shop::where("id", $shopId)->with('user')->first();
        $shop["luotban"] = OrdersModel::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->where('status', 2)
        ->where("shop_id", $shopId)->count();
        $listShop[] = $shop;
    }
    // dd($listShop);
    usort($listShop, function($a, $b) {
        return $b->luotban <=> $a->luotban;
    });
    // dd($listShop);
    return view('statist.sales',compact(
        'luongtrahangJson',
        'luotmuaJson',
        'bihuyJson',
        'loiJson',

        'luongtrahangThangJson',
        'luotmuaThangJson',
        'bihuyThangJson',
        'loiThangJson',

        'luongtrahangCacNamJson',
        'luotmuaCacNamJson',
        'bihuyCacNamJson',
        'loiCacNamJson',
        
        'TongSoLuongBanRa',
        'listShop',
        'DangGiao',
        'DoiTra',
        'Huy',
        'HoanThanh',
        'ThatBai',
        'ChoDuyet',
        'ChuaThanhToan',
    ));
}
public function revenue_general(Request $request){
    $token = $request->token; 
    $totalRevenue = order_fee_details::sum('amount');
    // return redirect()->route('revenue_general', [
    //     'token' => $token,
    //     'totalRevenue' => $totalRevenue,
    // ]);

    return view('revenue.revenue_general', compact('totalRevenue'));
}
public function logout(){
    return redirect()->route('login');
}


public function list_notification(Request $request){
        $limit = 20;
        $user = JWTAuth::parseToken()->authenticate();
        $notificationIds = Notification::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->where('status', 1)
        ->pluck('id_notification');
        $notificationMain = Notification_to_mainModel::whereIn('id', $notificationIds)
        ->orderBy('created_at', 'desc') // Thêm sắp xếp nếu cần
        ->paginate($limit);
        return view('notification.list_notification', compact('notificationMain'));
}

public function delete_notification(Request $request, $id)
{
    $notifi = Notification::where('id_notification', $id)->first();
    $notifi->status = 2;
    $notifi->save();
    return back()->with('message', 'Xóa thông báo thành công');

}
public function rankall(Request $request)
{
    $tab = $request->input('tab', 1); 
    $ranks = RanksModel::where('status',2)->orderBy('created_at', 'desc')->paginate(10);
    $ranks0ff = RanksModel::where('status',0)->orderBy('updated_at', 'desc')->paginate(10);

    return view('ranks.list_rank', compact('ranks', 'ranks0ff', 'tab'));  

}

public function rankCreate(Request $request)
{
    $token = $request->query('token');
    $tab = $request->input('tab', 1); 
    
    RanksModel::create([
        'title' => $request->title,
        'description' => $request->description,
        'condition' => $request->condition,
        'value' => $request->value,
        'limitValue' => $request->limitValue,
        'status' => $request->status,
        'create_by' => auth()->user()->id,
        'update_by' => null, 
    ]);
    
    return redirect()->route('rankall', [
        'token' => $token,
        'tab' => $tab,
    ])->with('message', 'Thêm rank thành công!');

}

    
public function updaterank(RankRequest $request, $id)
{
    $token = $request->token;
    $tab = $request->tab;

    $rank = RanksModel::findOrFail($id);
    $dataUpdate = [
        'title' => $request->title,
        'description' => $request->description,
        'condition' => $request->condition,
        'status' => $request->status,
        'value' => $request->value,
        'limitValue' => $request->limitValue,
        'update_by' => auth()->user()->id,
    ];

    try {
        $rank->update($dataUpdate);
        return redirect()->route('rankall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Cập nhật rank thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('rankall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('error', 'Cập nhật rank thất bại: ' . $th->getMessage());
    }
}

public function changeStatusRank(Request $request, string $id)
{
    try {
        $token = $request->token;
        $tab = $request->tab;
        $rank = RanksModel::findOrFail($id);
        $rank->status = $request->status;
        $rank->save();
        return redirect()->route('rankall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Cập nhật trạng thái thành công!');
    } catch (\Throwable $th) {
        // Xử lý lỗi và trả về thông báo
        return redirect()->route('rankall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
    }
}

public function changeStatusBanner(Request $request, string $id)
{
    try {
        $token = $request->token;
        $tab = $request->tab;
        $banner = Banner::findOrFail($id);
        $banner->status = $request->status;
        $banner->save();
        return redirect()->route('bannerall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Cập nhật trạng thái thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('bannerall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
    }
}
public function destroyrank(Request $request, string $id)
{
    try {
        $token = $request->token; 
        $tab = $request->tab; 
        $rank = RanksModel::findOrFail($id);
        $rank->delete();
        return redirect()->route('rankall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Xóa Rank thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('rankall', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Xóa Rank không thành công!');
    }
}

public function payment_method(Request $request)
{
    $tab = $request->input('tab', 1); 
    $payment_method = PaymentsModel::where('status',1)->orderBy('created_at', 'desc')->paginate(10);
    $payment_method0ff = PaymentsModel::where('status',0)->orderBy('updated_at', 'desc')->paginate(10);

    return view('payment_method.payment_method_list', compact('payment_method', 'payment_method0ff', 'tab'));  

}
public function storepaymant(PaymentRequest $request)
{

    $token = $request->query('token');
    $tab = $request->input('tab', 1); 
    $dataInsert = [
        "name" => $request->name,
        "code" => $request->code,
        "description" => $request->description,
        "status" => $request->status,
    ];

    try {
        PaymentsModel::create($dataInsert);
        return redirect()
            ->route('payment_method' , [
                'token' => $token,
                'tab' => $tab,
            ]) 
            ->with('success', 'Thêm phương thức thanh toán thành công');
    } catch (\Throwable $th) {
        return redirect()
            ->back()
            ->with('error', 'Thêm phương thức thanh toán không thành công: ' . $th->getMessage())
            ->withInput(); 
    }
}

public function updatepayment(PaymentRequest $request, $id)
{
    $payment = PaymentsModel::findOrFail($id);
    
    $token = $request->token;
    $tab = $request->tab;
    $dataUpdate = [
        "name" => $request->name,
        "code" => $request->code,
        "description" => $request->description,
        "status" => $request->status,
    ];

    try {
        $payment->update($dataUpdate);

        return redirect()
        ->route('payment_method' , [
            'token' => $token,
            'tab' => $tab,
        ]) ->with('success', 'Cập nhật phương thức thanh toán thành công');
    } catch (\Throwable $th) {
        return redirect()
            ->back()
            ->with('error', 'Cập nhật phương thức thanh toán không thành công: ' . $th->getMessage())
            ->withInput();
    }
}

public function changeStatuspayment(Request $request, string $id)
{
    try {
        $token = $request->token;
        $tab = $request->tab;
        $payment = PaymentsModel::findOrFail($id);
        $payment->status = $request->status;
        $payment->save();
        return redirect()->route('payment_method', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Cập nhật trạng thái thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('payment_method', [
            'token' => $token,
            'tab' => $tab,
        ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
    }
}

public function destroypayment(Request $request, string $id)
{
    try {
        $token = $request->token; 
        $tab = $request->tab; 
        $payment = PaymentsModel::findOrFail($id);
        $payment->delete();
        return redirect()->route('payment_method', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Xóa payment_method thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('payment_method', [
            'token' => $token,
            'tab' => $tab,
        ])->with('message', 'Xóa payment_method không thành công!');
    }
}

public function handleUpdateProduct(Request $request, string $id)
// ProductRequest
{
    $tab = $request->tab;
    try {
        if ($request->action == 1) {
            $newDT = DB::table("update_product")->orderBy("updated_at", "desc")->where("product_id", $id)->first();
            $ollDT = DB::table("products")->where("id", $id)->first();
            $ollData = (array) $ollDT;
            $newData = (array) $newDT;
            DB::table('products_old')->insert($ollData);
            $change_of = $data = json_decode($newData["change_of"], true);
            unset($newData["change_of"]);
            $newData["created_at"] = $newData["updated_at"];
            $newData["id"] = $newData["product_id"];
            unset($newData["product_id"]);
            DB::table('products')->where('id', $id)->update($newData);
            DB::table('update_product')->where('product_id', $newDT->product_id)->delete();
    
            if (json_decode($newDT->change_of) != 0) {
                foreach (json_decode($newDT->change_of) as $data) {
                    $variant = product_variants::find($data->id);
                    if (!$variant) {
                        return response()->json([
                            'status' => false,
                            'message' => "Không tồn tại biến thể nào",
                        ], 404);
                    }
    
                    $variant->update([
                        'sku' => $data->sku,
                        'stock' => $data->stock,
                        'price' => $data->price,
                        'images' => $data->images,
                    ]);
                }
            }
    
            $product = Product::find($id);
            $product_variants_get_price = product_variants::where('product_id', $product->id)->get();
            $highest_price = $product_variants_get_price->max('price');
            $lowest_price = $product_variants_get_price->min('price');
    
            if ($highest_price == $lowest_price) {
                $product->update([
                    'show_price' => $highest_price,
                ]);
            }
            if ($highest_price != $lowest_price) {
                $product->update([
                    'show_price' => $lowest_price . " - " . $highest_price,
                ]);
            }
    
            return redirect()->route('product_all', [
                'token' => auth()->user()->refesh_token,
                'tab' => $tab
            ])->with('message', 'Đã cập nhật sản phẩm.');
    
        } else {
            DB::table('update_product')->where('product_id', $id)->delete();
    
            return redirect()->route('product_all', [
                'token' => auth()->user()->refesh_token,
                'tab' => $tab
            ])->with('error', 'Từ chối cập nhật.');
        }
    } catch (\Exception $e) {
        return redirect()->route('product_all', [
            'token' => auth()->user()->refesh_token,
            'tab' => $tab
        ])->with('error', 'Đã xảy ra lỗi không mong muốn: ' . $e->getMessage());
    }
    
}
//events -------------------------------------------------------------------------------------
public function listEvent(Request $request)
{
    // dd("ok");
    $token = $request->token; 
    try {
        $events = Event::whereIn('status', [1, 2])->orderBy('id', 'desc')->paginate(10);
        foreach ($events as &$event) {
            $event['voucher_apply'] = json_decode($event['voucher_apply'], true); // Giải mã JSON
            $event->images = json_decode($event->images, true);
        }
        return view('events.list_event',compact('events'));
    } catch (\Throwable $th) {
        return redirect()->route('events', [
            'token' => $token
        ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
    }
}

public function listEvent_trash(Request $request)
{
    $token = $request->token; 
    try {
      
        $trash_events = Event::whereIn('status', [5])->paginate(10);
        foreach ($trash_events as &$event) {
            $event['voucher_apply'] = json_decode($event['voucher_apply'], true); // Giải mã JSON
            $event->images = json_decode($event->images, true);
        }
        return view('events.trash_event',compact('trash_events'));
    } catch (\Throwable $th) {
        return redirect()->route('trash_events', [
            'token' => $token
        ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
    }
}


public function changeStatusEvent(Request $request)
{  
    // dd($request->status);
    try {
        $event =Event::find($request->id);
        // $event = PaymentsModel::findOrFail($id);
        $event->status = $request->status;
        $event->save();
        return back()->with('message', 'Cập nhật trạng thái thành công!');
    } catch (\Throwable $th) {
        return redirect()->route('events', [
            'token' => $request->token
        ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
    }
}


public function store_events(Request $request)
{
    $token = $request->token; 

    if (strtotime($request->from) >= strtotime($request->to)) {
        return redirect()->back()->with('error', 'Ngày bắt đầu phải nhỏ hơn ngày kết thúc.');
    }
    try {
        DB::beginTransaction();
        $voucher_apply = [
            'voucher_title' => $request->voucher_apply ?? null,
            'voucher_description' => $request->voucher_description ?? null,
            'voucher_quantity' => $request->voucher_quantity ?? null,
            'voucher_limit' => $request->voucher_limit ?? null,
            'voucher_ratio' => $request->voucher_ratio ?? null,
            'voucher_code' => $request->voucher_code ?? null,
        ];
        if ($request->event_image  ) {
            $images = [];
            foreach ($request->event_image as $image) {
                $images[] = $this->storeImage($image);
            }
        }
        // $query = Product::where('status', 2);
        // $categoryForProduct = CategoriesModel::whereIn('id', $query->pluck('category_id'))->select('id')->get();
        // $tax_category = tax_category::whereIn('category_id', $categoryForProduct->pluck('id'))->select('category_id')->get();
        // $query->whereIn('category_id', $tax_category->pluck('category_id'));
        // $shopForProduct = Shop::whereIn('id', $query->pluck('shop_id'))->where('status', 2)->select('id')->get();
        // $query->whereIn('shop_id', $shopForProduct->pluck('id'));
        // $product_apply = [];
        // $shop_apply = [];
        // $query2 = Shop::where('status', 2);
        // if ($request->shop_where_visits) {
        //     $query2->where('visits', '>=', $request->shop_where_visits);
        // }
        // if ($request->shop_where_product_sold_count) {
        //     $shopHasProActive = Product::whereIn('shop_id', $query2->pluck('id'))->where('status', 2)->select('shop_id')->get();
        //     $query2->whereIn('id', $shopHasProActive->pluck('shop_id'));
        // }
        // if ($request->product_view) {
        //     $query->where('view_count' ,'>=', $request->product_view);
        // }
        // if ($request->product_sold_count) {
        //     $query->where('sold_count' ,'>=', $request->product_sold_count);
        // }
        // $product_apply = $query->pluck('id');
        // $shop_apply = $query2->pluck('id');
        $event = new Event();
        $event->event_title = $request->input('event_title', $event->event_title);
        $event->event_day = $request->input('event_day', $event->event_day);
        $event->event_month = $request->input('event_month', $event->event_month);
        $event->event_year = $request->input('event_year', $event->event_year);
        $event->qualifier = $request->input('qualifier', $event->qualifier?? null);
        $event->voucher_apply = json_encode($voucher_apply);
        $event->is_mail = $request->has('is_mail') ? $request->input('is_mail') : $event->is_mail;
        $event->point = $request->input('point', $event->point);
        $event->is_share_facebook = $request->has('is_share_facebook') ? $request->input('is_share_facebook') : $event->is_share_facebook;
        $event->is_share_zalo = $request->has('is_share_zalo') ? $request->input('is_share_zalo') : $event->is_share_zalo;
        $event->where_order = $request->input('where_order', $event->where_order);
        $event->where_price = $request->input('where_price', $event->where_price);
        $event->date = $request->input('date', $event->date);
        $event->from = $request->from;
        $event->to = $request->to;
        $event->status = $request->input('status', $event->status);
        $event->description = $request->input('description', $event->description);
        $event->images = json_encode($images);
        $event->product_apply = json_encode($product_apply ?? null);
        $event->shop_apply = json_encode($shop_apply ?? null);
        $event->save();
        DB::commit();
        return redirect()
            ->route('events',[
                'token' => $token
            ]) 
            ->with('success', 'Thêm sự kiện thành công!');
    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()
            ->back() 
            ->with('error', 'Thêm sự kiện không thành công: ' . $th->getMessage());
    }
}
public function update_events(Request $request, $id)
{
    $token = $request->input('token'); 
    $event = Event::find($id);
   
    if (!$event) {
        return redirect()->back()->with('error', 'Không tìm thấy sự kiện.');
    }

    // Xử lý voucher
    $voucher_apply = [
        'voucher_title' => $request->input('voucher_title'),
        'voucher_description' => $request->input('voucher_description'),
        'voucher_quantity' => $request->input('voucher_quantity'),
        'voucher_limit' => $request->input('voucher_limit'),
        'voucher_ratio' => $request->input('voucher_ratio'),
        'voucher_code' => $request->input('voucher_code'),
    ];
    $voucher_apply = array_filter($voucher_apply, function ($value) {
        return !is_null($value);
    });
    $existingImages = json_decode($event->images, true) ?? []; 
    $newImages = [];
    if ($request->hasFile('event_image')) {
        foreach ($request->file('event_image') as $image) {
            $newImages[] = $this->storeImage($image); 
        }
    }
    $event->images = json_encode(!empty($newImages) ? $newImages : $existingImages);

    try {
        $event->event_title = $request->input('event_title', $event->event_title);
        $event->event_day = $request->input('event_day', $event->event_day);
        $event->event_month = $request->input('event_month', $event->event_month);
        $event->event_year = $request->input('event_year', $event->event_year);
        $event->voucher_apply = !empty($voucher_apply) ? json_encode($voucher_apply) : $event->voucher_apply;
        $event->is_mail = $request->has('is_mail') ? $request->boolean('is_mail') : $event->is_mail;
        $event->point = $request->input('point', $event->point);
        $event->is_share_facebook = $request->has('is_share_facebook') ? $request->boolean('is_share_facebook') : $event->is_share_facebook;
        $event->is_share_zalo = $request->has('is_share_zalo') ? $request->boolean('is_share_zalo') : $event->is_share_zalo;
        $event->where_order = $request->input('where_order', $event->where_order);
        $event->where_price = $request->input('where_price', $event->where_price);
        $event->date = $request->input('date', $event->date);
        $event->from = $request->input('from', $event->from);
        $event->to = $request->input('to', $event->to);
        $event->status = $request->input('status', $event->status);
        $event->description = $request->input('description', $event->description);
        $event->save();
      return back()->with('message', 'Cập nhật sự kiện thành công.');
   

        // return redirect()
        //     ->route('events', ['token' => $token])
        //     ->with('success', 'Cập nhật sự kiện thành công.');
    } catch (\Throwable $th) {
        return redirect()->back()->with('error', 'Cập nhật sự kiện không thành công: ' . $th->getMessage());
    }
}



// public function changeStatuspayment(Request $request, string $id)
// {
//     try {
        
//         return redirect()->route('events', [
//             'token' => $token
//         ])->with('message', 'Cập nhật trạng thái thành công!');
//     } catch (\Throwable $th) {
//         return redirect()->route('events', [
//             'token' => $token
//         ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
//     }
// }
// public function changeStatuspayment(Request $request, string $id)
// {
//     try {
        
//         return redirect()->route('events', [
//             'token' => $token
//         ])->with('message', 'Cập nhật trạng thái thành công!');
//     } catch (\Throwable $th) {
//         return redirect()->route('events', [
//             'token' => $token
//         ])->with('error', 'Cập nhật trạng thái thất bại: ' . $th->getMessage());
//     }
// }


    public function checkWebDie()
    {
        checkWebDie::dispatch();
        // try {
        //     $urlAPI = 'http://vnshop.top/';
        //     $urlClient = 'https://test.vnshop.top/';
        //     $client = new Client();
        //     $responseAPI = $client->request('GET', $urlAPI);
        //     $responseClient = $client->request('GET', $urlClient);
        //     $statusCode = $responseAPI->getStatusCode();
        //     $statusCodeClient = $responseClient->getStatusCode();
        //     if ($statusCode == 200 && $statusCodeClient == 200) {
        //         $message = 'Cả API và Client đều hoạt động bình thường';
        //         log_host($message, $statusCode, $urlAPI, $statusCodeClient, $urlClient);
        //     } else {
        //         $message = 'WEBSITE KHÔNG HOẠT ĐỘNG';
        //         log_host($message, $statusCode, $urlAPI, $statusCodeClient, $urlClient);
        //     }
        // } catch (\Throwable $th) {
        //     log_debug($th->getMessage());
        // } 

    }
    public function loinhuan(Request $request,)
    {
        $listShop = Shop::whereIn("status", [1, 2, 4])->with('user')->get();
        foreach ($listShop as $key => $shop) {
            $orders = OrdersModel::where("shop_id", $shop->id)->get();
            $fee = 0;
            foreach ($orders as $order) {
                $fee += order_fee_details::where("order_id", $order->id)->sum('amount');
            }
            $shop['fee'] = $fee;
        }
        return view('loinhuan.loinhuan', compact('listShop'));
    }
    public function send_mail_event()
    {
        EventMail::dispatch();
        return back()->with('message','Chương trình đã được kích hoạt');
    }
    public function check_time_event()
    {
        check_time_event::dispatch();
       return back()->with('message','Chương trình đã dừng');
    }

    public function check_time_event_hand(Request $request)
    {
        check_time_event_hand::dispatch($request->id);
       return back()->with('message','Chương trình đã dừng');
    }
    



}


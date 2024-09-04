<?php

namespace App\Http\Controllers;

use App\Http\Controllers\NotificationController;
use App\Models\CommentsModel;
use App\Http\Requests\CommentsRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Comments = CommentsModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Comments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentsRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $dataInsert = [
            "title" => $request->title,
            "content" => $request->content,
            "rate" => $request->rate,
            "status" => $request->status,
            "parent_id" => $request->parent_id,
            "product_id" => $request->product_id,
            "user_id" => $user->id,
            "created_at" => now()
        ];

        $notificationController = new NotificationController();
        $comment = CommentsModel::create($dataInsert);


        if (is_null($request->parent_id)) {
            Cache::put('parent_comment_' . $comment->id, $comment, 30 * 60);
        } elseif ($request->parent_id) {
            $parent_comment = Cache::remember('parent_comment_' . $request->parent_id, 30 * 60, function () use ($request) {
                return CommentsModel::find($request->parent_id);
            });

            if ($parent_comment) {
                $parent_user_id = $parent_comment->user_id;
                $notificationRequest = new Request([
                    'type' => 'main',
                    'user_id' => $parent_user_id,
                    'title' => 'Có phản hồi mới từ comment của bạn',
                    'description' => $user->fullname . ' đã phản hồi comment của bạn.',
                ]);
                $notificationController->store($notificationRequest);
            }
        }

        $product = Product::find($request->product_id);

        $notificationRequest = new Request([
            'type' => 'shop',
            'user_id' => $user->id,
            'title' => 'Thông báo từ Sản Phẩm',
            'description' => $user->fullname . ' đã gửi một bình luận đến sản phẩm của bạn.',
            'shop_id' => $product->shop_id
        ]);
        $notificationController->store($notificationRequest);

        $dataDone = [
            'status' => true,
            'message' => "Đã lưu comment",
            'data' => $dataInsert,
        ];

        return response()->json($dataDone, 200);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Comments = CommentsModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Comments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentsRequest $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $comment = CommentsModel::find($id);

        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Không có comment nào'], 404);
        }

        $dataUpdate = [
            "title" => $request->title,
            "content" => $request->content,
            "rate" => $request->rate,
            "status" => $request->status,
            "parent_id" => $request->parent_id,
            "product_id" => $request->product_id,
            "user_id" => $user->id,
            "updated_at" => now()
        ];

        $comment->update($dataUpdate);

        if (is_null($request->parent_id)) {
            Cache::put('parent_comment_' . $comment->id, $comment, 30 * 60);
        } else {
            $parent_comment = Cache::remember('parent_comment_' . $request->parent_id, 30 * 60, function () use ($request) {
                return CommentsModel::find($request->parent_id);
            });

            if ($parent_comment) {
                $parent_user_id = $parent_comment->user_id;
                $notificationRequest = new Request([
                    'type' => 'main',
                    'user_id' => $parent_user_id,
                    'title' => 'Có cập nhật mới từ comment của bạn',
                    'description' => $user->fullname . ' đã phản hồi comment của bạn.',
                ]);
                $notificationController = new NotificationController();
                $notificationController->store($notificationRequest);
            }
        }

        $product = Product::find($request->product_id);

        $notificationRequest = new Request([
            'type' => 'shop',
            'user_id' => $user->id,
            'title' => 'Thông báo cập nhật từ Sản Phẩm',
            'description' => $user->fullname . ' đã cập nhật một bình luận đến sản phẩm của bạn.',
            'shop_id' => $product->shop_id
        ]);
        $notificationController = new NotificationController();
        $notificationController->store($notificationRequest);

        $dataDone = [
            'status' => true,
            'message' => "Đã cập nhật comment",
            'data' => $dataUpdate,
        ];

        return response()->json($dataDone, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Comments = CommentsModel::findOrFail($id);
            $Comments->delete();
            return response()->json([
                'status' => "success",
                'message' => 'Xóa thành công',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\WishlistModel;
use App\Http\Requests\WishlistRequest;
use Illuminate\Support\Facades\Cache;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wishlists = Cache::remember('all_wishlists', 60 * 60, function () {
            return WishlistModel::all();
        });

        if ($wishlists->isEmpty()) {
            return $this->errorResponse("Không tồn tại Wishlist nào");
        }

        return $this->successResponse('Lấy dữ liệu thành công', $wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WishlistRequest $request)
    {
        try {
            $wishlist = WishlistModel::create($request->validated());
            Cache::forget('all_wishlists');
            return $this->successResponse("Thêm Wishlist thành công", $wishlist);
        } catch (\Exception $e) {
            return $this->errorResponse("Thêm Wishlist không thành công", $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $wishlist = Cache::remember('wishlist_' . $id, 60 * 60, function () use ($id) {
            return WishlistModel::find($id);
        });

        if (!$wishlist) {
            return $this->errorResponse("Wishlist không tồn tại", null, 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $wishlist);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WishlistRequest $request, string $id)
    {
        $wishlist = Cache::remember('wishlist_' . $id, 60 * 60, function () use ($id) {
            return WishlistModel::find($id);
        });

        if (!$wishlist) {
            return $this->errorResponse("Wishlist không tồn tại", null, 404);
        }

        try {
            $wishlist->update($request->validated());
            Cache::forget('wishlist_' . $id);
            Cache::forget('all_wishlists');
            return $this->successResponse("Wishlist đã được cập nhật", $wishlist);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật Wishlist không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $wishlist = Cache::remember('wishlist_' . $id, 60 * 60, function () use ($id) {
            return WishlistModel::find($id);
        });

        if (!$wishlist) {
            return $this->errorResponse("Wishlist không tồn tại", null, 404);
        }

        try {
            $wishlist->update(['status' => 101]);
            Cache::forget('wishlist_' . $id);
            Cache::forget('all_wishlists');
            return $this->successResponse("Wishlist đã được xóa");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa Wishlist không thành công", $th->getMessage());
        }
    }

    /**
     * Return success response
     */
    private function successResponse(string $message, $data = null, int $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Return error response
     */
    private function errorResponse(string $message, $error = null, int $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}

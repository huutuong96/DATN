<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Cloudinary\Cloudinary;
class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $images = Image::all();
        // if($images->isEmpty()){
        //     return response()->json(
        //         [
        //             'status' => true,
        //             'message' => "Không tồn tại ảnh nào nào",
        //         ]
        //     );
        // }
        // return response()->json(
        //     [
        //         'status' => true,
        //         'message' => "Lấy dữ liệu thành công",
        //         'data' => $images,
        //     ]
        // );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // if ($request->hasFile('image')) {
        //     $cloudinary = new Cloudinary();
        //     $uploadedImage = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
        //     $imageUrl = $uploadedImage['secure_url'];
        // }

        // try {


            // $dataInsert = [
            //     'product_id' => $request->product_id,
            //     'url' => $imageUrl,
            //     'status' =>  $request->status,
            // ];

        //     $image = Image::create($dataInsert);

        //     return response()->json([
        //         'status' => true,
        //         'message' => "Thêm image thành công",
        //         'data' => $image,
        //     ], 200);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Thêm image không thành công",
        //         'error' => $th->getMessage(),
        //     ], 500);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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
    public function update(Request $request, string $id)
    {
        // $image = Image::find($id);

        // if (!$image) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "image không tồn tại",
        //     ], 404);
        // }

        // if ($rqt->hasFile('image')) {
        //     $image = $rqt->file('image');
        //     $cloudinary = new Cloudinary();
        //     $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        //     $imageUrl = $uploadedImage['secure_url'];
        // } else {
        //     $imageUrl = $image->URL;
        // }

        // $dataUpdate = [
        //     'product_id' => $request->title,
        //     'url' =>$imageUrl,
        //     'status' =>  $request->content,
        // ];

        // try {
        //     $image->update($dataUpdate);
        //     return response()->json([
        //         'status' => true,
        //         'message' => "Cập nhật hình ảnh thành công",
        //         'data' => $image,
        //     ]);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Cập nhật hinh ảnh không thành công",
        //         'error' => $th->getMessage(),
        //     ]);
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // try {
        //     $image = Image::find($id);

        //     if (!$image) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'image không tồn tại',
        //         ], 404);
        //     }

        //     $image->delete();

        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Xóa image thành công',
        //     ]);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Xóa image không thành công",
        //         'error' => $th->getMessage(),
        //     ]);
        // }
    }
}

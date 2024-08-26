<?php

namespace App\Http\Controllers;
use App\Models\Programme_detail;
use Illuminate\Http\Request;

class ProgrameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programe = Programme_detail::all();
        if($programe->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại chương trình nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $programe,
            ]
        );
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
        $dataInsert = [
            'title' => $request->title,
            'content' => $request->content,
        ];

        try {
            $programe = Programme_detail::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm chương trình thành công",
                    'data' => $programe,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm chương trình không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $programe = Programme_detail::find($id);
        if(!$programe){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại chương trình nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $programe,
            ]
        );
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
        // Tìm chương trình theo ID
        $programe = Programme_detail::find($id);
        if (!$programe) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Chương trình không tồn tại",
                ],
                404
            );
        }
        // Cập nhật dữ liệu
        $dataUpdate = [
            'title' => $request->title ?? $programe->title,
            'content' => $request->content ?? $programe->content,
            'update_at' => now(), // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];


        try {
            // Cập nhật bản ghi
            $programe->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật chương trình thành công",
                    'data' => $programe,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật chương trình không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $programe = Programme_detail::find($id);

            if (!$programe) {
                return response()->json([
                    'status' => false,
                    'message' => 'chương trình không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $programe->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa chương trình thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa chương trình không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}

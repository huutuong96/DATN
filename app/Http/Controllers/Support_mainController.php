<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\support_main;
class Support_mainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supports = support_main::all();
        if($supports->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại hỗ trợ nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $supports,
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
            'content' => $request->content,
            'status' => $request->status,
            'index' => $request->index,
            'category_support_id' => $request->category_support_id,
        ];

        try {
            $support = support_main::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm hỗ trợ thành công",
                    'data' => $support,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm hỗ trợ không thành công",
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
        $support = support_main::find($id);
        if(!$support){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại support nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $support,
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
        // Tìm banner theo ID
        $support = support_main::find($id);
        // Kiểm tra xem support_main có tồn tại không
        if (!$support) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "support không tồn tại",
                ],
                404
            );
        }
        // Cập nhật dữ liệu
        $dataUpdate = [
            'content' => $request->content ?? $support->content,
            'status' => $request->status ?? $support->status,
            'index' => $request->index ?? $support->index,
            'category_support_id' => $request->category_support_id ?? $support->category_support_id,
        ];


        try {
            // Cập nhật bản ghi
            $support->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật support thành công",
                    'data' => $support,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật support không thành công",
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
            $support = support_main::find($id);

            if (!$support) {
                return response()->json([
                    'status' => false,
                    'message' => 'support không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $support->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa support thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa support không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}

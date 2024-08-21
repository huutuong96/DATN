<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FAQRequest;
use App\Models\FAQ;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = FAQ::all();
        if($faqs->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại faq nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $faqs,
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
    public function store(FAQRequest $rqt)
    {
        $dataInsert = [
            'title' => $rqt->title,
            'content' => $rqt->content,
            'status' => $rqt->status,
            'index' => $rqt->index,
        ];

        try {
            $faq = FAQ::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm FAQ thành công",
                    'data' => $faq,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm FAQ không thành công",
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
        $faq = FAQ::find($id);
        if(!$faq){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại faq nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $faq,
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
    public function update(FAQRequest $rqt, string $id)
    {
        // Tìm faq theo ID
        $faq = FAQ::find($id);

        // Kiểm tra xem rqt có tồn tại không
        if (!$faq) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "faq không tồn tại",
                ],
                404
            );
        }

        // Cập nhật dữ liệu
        $dataUpdate = [
            'title' => $rqt->title,
            'content' => $rqt->content,
            'status' => $rqt->status,
            'index' => $rqt->index,
            'created_at' => $rqt->created_at ?? $faq->created_at, // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];

        try {
            // Cập nhật bản ghi
            $faq->update($dataUpdate);

            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật rqt thành công",
                    'data' => $faq,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật faq không thành công",
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
            $faq = FAQ::find($id);

            if (!$faq) {
                return response()->json([
                    'status' => false,
                    'message' => 'faq không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $faq->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa faq thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa faq không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}

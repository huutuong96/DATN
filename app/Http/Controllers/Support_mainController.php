<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\support_main;
use Illuminate\Support\Facades\Cache;

class Support_mainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Chưa động đến nhưng đã lưu vào cache
        // Tên Controller là Support_mainController

        $supports = Cache::remember('all_supports', 60 * 60, function () {

            return support_main::all();

        });

        if ($supports->isEmpty()) {
            return $this->errorResponse("Không tồn tại hỗ trợ nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $supports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required',
            'status' => 'required',
            'index' => 'required',
            'category_support_id' => 'required',
            
        ]);

        try {
            $validatedData['create_by'] = auth()->user()->id;
            $support = support_main::create($validatedData);
            Cache::forget('all_supports');
            return $this->successResponse("Thêm hỗ trợ thành công", $support);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm hỗ trợ không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $support = Cache::remember('support_' . $id, 60 * 60, function () use ($id) {
            return support_main::find($id);
        });

        if (!$support) {
            return $this->errorResponse("Không tồn tại hỗ trợ nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $support);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $support = support_main::find($id);

        if (!$support) {
            return $this->errorResponse("Hỗ trợ không tồn tại", 404);
        }

        $validatedData = $request->validate([
            'content' => 'sometimes|required',
            'status' => 'sometimes|required',
            'index' => 'sometimes|required',
            'category_support_id' => 'sometimes|required|exists:category_supports,id',
        ]);

        try {
            $support->update($validatedData);
            Cache::forget('support_' . $id);
            Cache::forget('all_supports');
            return $this->successResponse("Cập nhật hỗ trợ thành công", $support);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật hỗ trợ không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy(string $id)
    {
        try {
            $support = support_main::findOrFail($id);
            $support->delete();
            Cache::forget('support_' . $id);
            Cache::forget('all_supports');
            return $this->successResponse("Xóa hỗ trợ thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa hỗ trợ không thành công", $th->getMessage());
        }
    }

    /**
     * Return a success response.
     */
    private function successResponse($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return an error response.
     */
    private function errorResponse($message, $error = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error,
        ], $code);
    }
}
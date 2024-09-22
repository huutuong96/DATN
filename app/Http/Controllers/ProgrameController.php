<?php

namespace App\Http\Controllers;

use App\Models\ProgramtoshopModel;
use Illuminate\Http\Request;
use App\Models\Programme_detail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\ProgrameRequest;

class ProgrameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $programs = Cache::remember('all_programs', 60 * 60, function () {
            return ProgramtoshopModel::where('shop_id')->get();
        });
        if ($programs->isEmpty()) {
            return $this->errorResponse("Không tồn tại chương trình nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $programs);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgrameRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $validatedData = $request->validated();
            $validatedData['create_by'] = $user->id;
            $program = Programme_detail::create($validatedData);
            
            Cache::forget('all_programs');
            return $this->successResponse("Thêm chương trình thành công", $program);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm chương trình không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program = Cache::remember('program_' . $id, 60 * 60, function () use ($id) {
            return Programme_detail::find($id);
        });

        if (!$program) {
            return $this->errorResponse("Chương trình không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $program);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgrameRequest $request, string $id)
    {
        $program = Programme_detail::find($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$program) {
            return $this->errorResponse("Chương trình không tồn tại", 404);
        }

        try {

            $validatedData = $request->validated();
            $validatedData['update_by'] = $user->id;
            $program->update($validatedData);
            Cache::forget('program_' . $id);
            Cache::forget('all_programs');
            return $this->successResponse("Cập nhật chương trình thành công", $program);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật chương trình không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $program = Programme_detail::findOrFail($id);
            $program->delete();
            Cache::forget('program_' . $id);
            Cache::forget('all_programs');
            return $this->successResponse("Xóa chương trình thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa chương trình không thành công", $th->getMessage());
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

<?php
namespace App\Http\Controllers;
use App\Models\Voucher;

use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucher = Voucher::all();
        if($voucher->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại voucher nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $voucher,
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
            'type' => $request->type,
            'status' => $request->status,
            // 'URL' => $uploadedImage['secure_url'],
            'code' => $request->code,
            // 'create_by',
            // 'update_by',
        ];

        try {
            $voucher = Voucher::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm voucher thành công",
                    'data' => $voucher,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm voucher không thành công",
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
        $voucher = Voucher::find($id);

        if(!$voucher){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại voucher nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $voucher,
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
        // $image = $rqt->file('image');
        // if ($image) {
        //     $cloudinary = new Cloudinary();
        //     $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        // }
        // Tìm voucher theo ID
        $voucher = Voucher::find($id);
        // Kiểm tra xem voucher có tồn tại không
        if (!$voucher) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "voucher không tồn tại",
                ],
                404
            );
        }
        // Cập nhật dữ liệu
        $dataUpdate = [
            'type' => $request->type ?? $voucher->type,
            'status' => $request->status ?? $voucher->status,
            'code' => $request->code ?? $voucher->code,
            'update_at' => now(), // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];


        try {
            // Cập nhật bản ghi
            $voucher->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật voucher thành công",
                    'data' => $voucher,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật voucher không thành công",
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
            $voucher = Voucher::find($id);

            if (!$voucher) {
                return response()->json([
                    'status' => false,
                    'message' => 'voucher không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $voucher->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa voucher thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa voucher không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}

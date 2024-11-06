<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use App\Models\platform_fees;
use App\Models\order_tax_details;
use App\Models\order_fee_details;


class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::all();

        if ($taxes->isEmpty()) {
            return $this->errorResponse("Không tồn tại thuế nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $taxes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request)
    {
        try {
            // Tạo mới một bản ghi thuế từ dữ liệu hợp lệ
            $tax = Tax::create($request->validated());
    
            // Chuyển hướng về trang danh sách thuế kèm thông báo thành công
            return redirect()->route('tax.index')->with('success', 'Thêm thuế thành công');
        } catch (\Throwable $th) {
            // Nếu có lỗi, chuyển hướng lại form thêm với thông báo lỗi
            return redirect()->back()->withInput()->with('error', 'Thêm thuế không thành công: ' . $th->getMessage());
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tax = Tax::find($id);

        if (!$tax) {
            return $this->errorResponse("Không tồn tại thuế nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $tax);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, string $id)
    {
        $tax = Tax::find($id);

        if (!$tax) {
            return $this->errorResponse("Thuế không tồn tại", 404);
        }

        try {
            $tax->update($request->validated());
            return $this->successResponse("Cập nhật thuế thành công", $tax);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật thuế không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tax = Tax::findOrFail($id);
            $tax->delete();
            return $this->successResponse("Xóa thuế thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa thuế không thành công", $th->getMessage());
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

    /**
     * Display a listing of platform fees.
     */
    public function indexPlatformFees()
    {
        try {
            $platformFees = platform_fees::all();
            return $this->successResponse("Lấy danh sách phí nền tảng thành công", $platformFees);
        } catch (\Throwable $th) {
            return $this->errorResponse("Lấy danh sách phí nền tảng không thành công", $th->getMessage());
        }
    }

    /**
     * Store a newly created platform fee in storage.
     */
    public function storePlatformFee(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'rate' => 'required|numeric',
                'description' => 'nullable|string',
            ]);

            $platformFee = platform_fees::create($validatedData);
            return $this->successResponse("Tạo phí nền tảng thành công", $platformFee);
        } catch (\Throwable $th) {
            return $this->errorResponse("Tạo phí nền tảng không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified platform fee.
     */
    public function showPlatformFee(string $id)
    {
        try {
            $platformFee = platform_fees::findOrFail($id);
            return $this->successResponse("Lấy thông tin phí nền tảng thành công", $platformFee);
        } catch (\Throwable $th) {
            return $this->errorResponse("Lấy thông tin phí nền tảng không thành công", $th->getMessage());
        }
    }

    /**
     * Update the specified platform fee in storage.
     */
    public function updatePlatformFee(Request $request, string $id)
    {
        try {
            $platformFee = platform_fees::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'rate' => 'sometimes|required|numeric',
                'description' => 'nullable|string',
            ]);

            $platformFee->update($validatedData);
            return $this->successResponse("Cập nhật phí nền tảng thành công", $platformFee);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật phí nền tảng không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified platform fee from storage.
     */
    public function destroyPlatformFee(string $id)
    {
        try {
            $platformFee = platform_fees::findOrFail($id);
            $platformFee->delete();
            return $this->successResponse("Xóa phí nền tảng thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa phí nền tảng không thành công", $th->getMessage());
        }
    }

    /**
     * Display a listing of the order tax details.
     */
    public function indexOrderTaxDetails()
    {
        try {
            $orderTaxDetails = order_tax_details::all();
            return $this->successResponse("Lấy danh sách chi tiết thuế đơn hàng thành công", $orderTaxDetails);
        } catch (\Throwable $th) {
            return $this->errorResponse("Lấy danh sách chi tiết thuế đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Store a newly created order tax detail in storage.
     */
    public function storeOrderTaxDetail(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'tax_id' => 'required|exists:taxs,id',
                'amount' => 'required|numeric',
            ]);

            $orderTaxDetail = order_tax_details::create($validatedData);
            return $this->successResponse("Thêm chi tiết thuế đơn hàng thành công", $orderTaxDetail);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm chi tiết thuế đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified order tax detail.
     */
    public function showOrderTaxDetail(string $id)
    {
        try {
            $orderTaxDetail = order_tax_details::findOrFail($id);
            return $this->successResponse("Lấy thông tin chi tiết thuế đơn hàng thành công", $orderTaxDetail);
        } catch (\Throwable $th) {
            return $this->errorResponse("Lấy thông tin chi tiết thuế đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Update the specified order tax detail in storage.
     */
    public function updateOrderTaxDetail(Request $request, string $id)
    {
        try {
            $orderTaxDetail = order_tax_details::findOrFail($id);

            $validatedData = $request->validate([
                'order_id' => 'sometimes|required|exists:orders,id',
                'tax_id' => 'sometimes|required|exists:taxs,id',
                'amount' => 'sometimes|required|numeric',
            ]);

            $orderTaxDetail->update($validatedData);
            return $this->successResponse("Cập nhật chi tiết thuế đơn hàng thành công", $orderTaxDetail);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật chi tiết thuế đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified order tax detail from storage.
     */
    public function destroyOrderTaxDetail(string $id)
    {
        try {
            $orderTaxDetail = order_tax_details::findOrFail($id);
            $orderTaxDetail->delete();
            return $this->successResponse("Xóa chi tiết thuế đơn hàng thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa chi tiết thuế đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Display a listing of the order fee details.
     */
    public function indexOrderFeeDetails()
    {
        try {
            $orderFeeDetails = order_fee_details::all();
            return $this->successResponse("Lấy danh sách chi tiết phí đơn hàng thành công", $orderFeeDetails);
        } catch (\Throwable $th) {
            return $this->errorResponse("Lấy danh sách chi tiết phí đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Store a newly created order fee detail in storage.
     */
    public function storeOrderFeeDetail(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'platform_fee_id' => 'required|exists:platform_fees,id',
                'amount' => 'required|numeric',
            ]);

            $orderFeeDetail = order_fee_details::create($validatedData);
            return $this->successResponse("Thêm chi tiết phí đơn hàng thành công", $orderFeeDetail);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm chi tiết phí đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified order fee detail.
     */
    public function showOrderFeeDetail(string $id)
    {
        try {
            $orderFeeDetail = order_fee_details::findOrFail($id);
            return $this->successResponse("Lấy thông tin chi tiết phí đơn hàng thành công", $orderFeeDetail);
        } catch (\Throwable $th) {
            return $this->errorResponse("Lấy thông tin chi tiết phí đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Update the specified order fee detail in storage.
     */
    public function updateOrderFeeDetail(Request $request, string $id)
    {
        try {
            $orderFeeDetail = order_fee_details::findOrFail($id);

            $validatedData = $request->validate([
                'order_id' => 'sometimes|required|exists:orders,id',
                'platform_fee_id' => 'sometimes|required|exists:platform_fees,id',
                'amount' => 'sometimes|required|numeric',
            ]);

            $orderFeeDetail->update($validatedData);
            return $this->successResponse("Cập nhật chi tiết phí đơn hàng thành công", $orderFeeDetail);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật chi tiết phí đơn hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified order fee detail from storage.
     */
    public function destroyOrderFeeDetail(string $id)
    {
        try {
            $orderFeeDetail = order_fee_details::findOrFail($id);
            $orderFeeDetail->delete();
            return $this->successResponse("Xóa chi tiết phí đơn hàng thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa chi tiết phí đơn hàng không thành công", $th->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotification;
use Illuminate\Http\Request;
use App\Models\voucherToMain;
use App\Http\Requests\VoucherRequest;
use App\Models\User;
use Cloudinary\Cloudinary;

class VoucherToMainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucherMains = voucherToMain::all();

        if ($voucherMains->isEmpty()) {
            return $this->errorResponse('Không tồn tại voucher main nào');
        }

        return $this->successResponse('Lấy dữ liệu thành công', $voucherMains);
    }
    public function voucherall(Request $request)
    {
        $tab = $request->input('tab', 1); 
        $voucherMains = voucherToMain::where('status',2)->get();
        $inactiveVoucher = voucherToMain::where('status',0)->get();
    
       
    
        return view('voucher.voucher_list', compact('voucherMains','inactiveVoucher', 'tab'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(VoucherRequest $request)
    {
        $token = $request->query('token');
        $users = User::select('id')->get();
        if ($request->image_voucher) {
            $voucherImage = $this->storeImage($request->image_voucher);
        }
        $voucherMain = new voucherToMain();
        $voucherMain->title = $request->title;
        $voucherMain->description = $request->description;
        $voucherMain->quantity = $request->quantity;
        $voucherMain->limitValue = $request->limitValue;
        $voucherMain->ratio = $request->ratio;
        $voucherMain->code = $request->code;
        $voucherMain->status = $request->status;
        $voucherMain->min = $request->min_order;
        $voucherMain->image = $voucherImage ?? null;
        $voucherMain->create_by = auth()->user()->id;
        $voucherMain->save();
        try {
            $voucherMain->save();
            foreach ($users as $user) {
                SendNotification::dispatch($voucherMain->title, $voucherMain->description, $user->id, null, $voucherImage);
            }
            return redirect()->route('voucherall', [
                'token' => $token,
            ])->with('message', 'Thêm voucher main thành công');
        } catch (\Throwable $th) {
            return redirect()->route('voucherall', [
                'token' => $token,
            ])->with('error', 'Thêm voucher main không thành công: ' . $th->getMessage());
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucherMain = voucherToMain::find($id);

        if (!$voucherMain) {
            return $this->errorResponse("Không tồn tại voucher main nào", null, 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $voucherMain);
    }



    /**
     * Update the specified resource in storage.
     */
   
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucherMain = voucherToMain::find($id);

        if (!$voucherMain) {
            return $this->errorResponse("Voucher main không tồn tại", null, 404);
        }

        try {
            $voucherMain->delete();
            return $this->successResponse("Xóa voucher main thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa voucher main không thành công", $th->getMessage());
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

    private function storeImage($image)
    {
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        return $uploadedImage['secure_url'];
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // 1. Lấy danh sách tất cả các sự kiện
    public function index()
    {   
        
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => Event::all()
        ], 200);
    }

    // 2. Tạo mới sự kiện
    public function store(Request $request)
    {
       
        try {
            $event = Event::create($request->all());
            return response()->json([
                'status' => true,
                'message' => "Thêm event thành công",
                'data' => $event,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "tạo danh mục không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
        
    }

    // 3. Lấy chi tiết sự kiện
    public function show($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Thêm event thành công",
            'data' => $event,
        ], 200);

    }

    // 4. Cập nhật sự kiện
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        try {
        $data = [
            'event_title' => $request->input('event_title') ?? $event->event_title,
            'event_day' => $request->input('event_day') ?? $event->event_day,
            'event_month' => $request->input('event_month') ?? $event->event_month,
            'event_year' => $request->input('event_year') ?? $event->event_year,
            'qualifier' => $request->input('qualifier') ?? $event->qualifier,
            'voucher_apply' => $request->input('voucher_apply') ?? $event->voucher_apply,
            'is_mail' => $request->input('is_mail') ?? $event->is_mail,
            'point' => $request->input('point') ?? $event->point,
            'is_share_facebook' => $request->input('is_share_facebook') ?? $event->is_share_facebook,
            'is_share_zalo' => $request->input('is_share_zalo') ?? $event->is_share_zalo,
            'where_order' => $request->input('where_order') ?? $event->where_order,
            'where_price' => $request->input('where_price') ?? $event->where_price,
            'date' => $request->input('date') ?? $event->date,
            'from' => $request->input('from') ?? $event->from,
            'to' => $request->input('to') ?? $event->to,
            'status' => $request->input('status') ?? $event->status,
            'description' => $request->input('description') ?? $event->description,
        ];

        $event->update($data);

        return response()->json([
            'status' => true,
            'message' => "Cập nhật event thành công",
            'data' => $event,
        ], 200);
        
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "tạo danh mục không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // 5. Xóa sự kiện
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->update(['status' => 0]);

        return response()->json([
            'status' => true,
            'message' => "Xóa danh mục thành công",
        ], 200);
    }
}


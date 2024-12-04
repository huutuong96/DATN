<?php

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // 1. Lấy danh sách tất cả các sự kiện
    public function index()
    {
        return response()->json(Event::all(), 200);
    }

    // 2. Tạo mới sự kiện
    public function store(Request $request)
    {
        $data = $request->validate([
            'event_title' => 'required|string|max:255',
            'event_day' => 'nullable|integer|min:1|max:31',
            'event_month' => 'nullable|integer|min:1|max:12',
            'event_year' => 'nullable|integer|min:1900|max:2100',
            'qualifier' => 'nullable|string|max:255',
            'voucher_apply' => 'nullable|boolean',
            'is_mail' => 'nullable|boolean',
            'point' => 'nullable|integer',
            'is_share_facebook' => 'nullable|boolean',
            'is_share_zalo' => 'nullable|boolean',
            'where_order' => 'nullable|string|max:255',
            'where_price' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'from' => 'nullable|date_format:H:i',
            'to' => 'nullable|date_format:H:i',
            'status' => 'nullable|integer|in:0,1',
            'description' => 'nullable|string',
        ]);

        $event = Event::create($data);

        return response()->json($event, 201);
    }

    // 3. Lấy chi tiết sự kiện
    public function show($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event, 200);
    }

    // 4. Cập nhật sự kiện
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $data = $request->validate([
            'event_title' => 'required|string|max:255',
            'event_day' => 'nullable|integer|min:1|max:31',
            'event_month' => 'nullable|integer|min:1|max:12',
            'event_year' => 'nullable|integer|min:1900|max:2100',
            'qualifier' => 'nullable|string|max:255',
            'voucher_apply' => 'nullable|boolean',
            'is_mail' => 'nullable|boolean',
            'point' => 'nullable|integer',
            'is_share_facebook' => 'nullable|boolean',
            'is_share_zalo' => 'nullable|boolean',
            'where_order' => 'nullable|string|max:255',
            'where_price' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'from' => 'nullable|date_format:H:i',
            'to' => 'nullable|date_format:H:i',
            'status' => 'nullable|integer|in:0,1',
            'description' => 'nullable|string',
        ]);

        $event->update($data);

        return response()->json($event, 200);
    }

    // 5. Xóa sự kiện
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}


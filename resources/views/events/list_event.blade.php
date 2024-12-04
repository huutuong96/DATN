@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
        <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách event đang hoạt động</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Tên event</th>
                                            <th scope="col">Đk áp dụng/th>
                                            <th scope="col">Thông tin thêm</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($events->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có cửa hàng nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($events as $event)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $event->id }}</a></th>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $event->event_title ?? "Chưa đặt tên"}}
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal;">
                                                     Chỉ áp dụng chon những đơn trên {{ $event->where_price ?? "???"}}
                                                    </td>
                                                    <td>
                                                        
                                                        <ul>
                                                            <li>Mô tả:{{ $event->event_title ?? "Chưa nhập"}}</li>
                                                            <li>Từ ngày {{ $event->from }} 
                                                                đến ngày {{ $event->to}}</li>
                                                        </ul>
                                                    </td>
                                                    
                                                    <td>
                                                        <ul class="list-inline">
                                                            @if ($event->status == 2)
                                                                <li class="list-inline-item">
                                                                    <a 
                                                                        href="{{ route('change_status_events', [
                                                                                                            'token' => auth()->user()->refesh_token,
                                                                                                            'id' => $event->id,
                                                                                                            'status' => 1,
                                                                                                            ]) }}"
                                                                    >
                                                                    <button type="button" class="btn btn-secondary" title="Khóa">
                                                                        <i class="ri-lock-line align-middle"></i>
                                                                    </button>
                                                                    </a>
                                                                </li>
                                                            @elseif ($event->status == 1)
                                                                <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_status_events', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $event->id,
                                                                                                        'status' => 2,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-success" title="mở"> <i class="ri-check-line align-middle"></i></button>

                                                                </li>
                                                            @endif
                                                            <li class="list-inline-item">
                                                   <!-- Toggle Between Modals -->
                                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal{{ $event->id }}" title="Chỉnh sửa"><i class="ri-edit-line align-middle"></i></button>
                                                    <!-- First modal dialog -->
                                                    <div class="modal fade" id="firstmodal{{ $event->id }}" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered" style=" margin-left: 20%;">
                                                            <div class="modal-content" style="width:1000px">
                                                                <div class="modal-body text-center p-5" style="width:1000px">
                                                                <form id="addBlogForm"
                                                                                    action="{{ route('update_events', [
                                                                                        'token' => auth()->user()->refesh_token,
                                                                                    ]) }}"
                                                                                    method="POST" >
                                                                    @csrf
                                                                    <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="row g-3">
                <!-- Event Title -->
                <div class="col-md-6">
                    <label for="event_title" class="form-label">Event Title</label>
                    <input type="text" class="form-control" id="event_title" name="event_title" value="{{ old('event_title', $event->event_title ?? '') }}" required>
                </div>

                <!-- Event Day -->
                <div class="col-md-6">
                    <label for="event_day" class="form-label">Event Day</label>
                    <input type="number" class="form-control" id="event_day" name="event_day" min="1" max="31" value="{{ old('event_day', $event->event_day ?? '') }}">
                </div>

                <!-- Event Month -->
                <div class="col-md-6">
                    <label for="event_month" class="form-label">Event Month</label>
                    <input type="number" class="form-control" id="event_month" name="event_month" min="1" max="12" value="{{ old('event_month', $event->event_month ?? '') }}">
                </div>

                <!-- Event Year -->
                <div class="col-md-6">
                    <label for="event_year" class="form-label">Event Year</label>
                    <input type="number" class="form-control" id="event_year" name="event_year" min="1900" max="2100" value="{{ old('event_year', $event->event_year ?? '') }}">
                </div>

                <!-- Qualifier -->
                <div class="col-md-6">
                    <label for="qualifier" class="form-label">Qualifier</label>
                    <input type="text" class="form-control" id="qualifier" name="qualifier" value="{{ old('qualifier', $event->qualifier ?? '') }}">
                </div>

                <!-- Voucher Apply -->
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="voucher_apply" name="voucher_apply" value="1" {{ old('voucher_apply', $event->voucher_apply ?? 0) ? 'checked' : '' }}>
                        <label for="voucher_apply" class="form-check-label">Apply Voucher</label>
                    </div>
                </div>

                <!-- Is Mail -->
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="is_mail" name="is_mail" value="1" {{ old('is_mail', $event->is_mail ?? 0) ? 'checked' : '' }}>
                        <label for="is_mail" class="form-check-label">Send Email</label>
                    </div>
                </div>

                <!-- Points -->
                <div class="col-md-6">
                    <label for="point" class="form-label">Points</label>
                    <input type="number" class="form-control" id="point" name="point" value="{{ old('point', $event->point ?? 0) }}">
                </div>

                <!-- Share Facebook -->
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="is_share_facebook" name="is_share_facebook" value="1" {{ old('is_share_facebook', $event->is_share_facebook ?? 0) ? 'checked' : '' }}>
                        <label for="is_share_facebook" class="form-check-label">Share on Facebook</label>
                    </div>
                </div>

                <!-- Share Zalo -->
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="is_share_zalo" name="is_share_zalo" value="1" {{ old('is_share_zalo', $event->is_share_zalo ?? 0) ? 'checked' : '' }}>
                        <label for="is_share_zalo" class="form-check-label">Share on Zalo</label>
                    </div>
                </div>

                <!-- Where Order -->
                <div class="col-md-6">
                    <label for="where_order" class="form-label">Order Location</label>
                    <input type="text" class="form-control" id="where_order" name="where_order" value="{{ old('where_order', $event->where_order ?? '') }}">
                </div>

                <!-- Where Price -->
                <div class="col-md-6">
                    <label for="where_price" class="form-label">Order Price</label>
                    <input type="number" step="0.01" class="form-control" id="where_price" name="where_price" value="{{ old('where_price', $event->where_price ?? 0) }}">
                </div>

                <!-- Date -->
                <div class="col-md-6">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $event->date ?? '') }}">
                </div>

                <!-- From -->
                <div class="col-md-6">
                    <label for="from" class="form-label">From Time</label>
                    <input type="time" class="form-control" id="from" name="from" value="{{ old('from', $event->from ?? '') }}">
                </div>

                <!-- To -->
                <div class="col-md-6">
                    <label for="to" class="form-label">To Time</label>
                    <input type="time" class="form-control" id="to" name="to" value="{{ old('to', $event->to ?? '') }}">
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" {{ old('status', $event->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $event->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $event->description ?? '') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary mt-3">Update Event</button>
                </div>
            </div>
                                                                </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_status_events', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $event->id,
                                                                                                        'status' => 4,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-warning" title="vi pham"> <i class=" ri-close-line align-middle"></i></button>
                                                            </li>  
                                                        </ul>
                                                        
                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mt-3">
                                    {{ $events->appends(['token' => auth()->user()->refesh_token])->links() }}
                                </div>
                                <a
                                    href="{{ route('trash_stores',['token' => auth()->user()->refesh_token]) }}"
                                    class="nav-link text-primary"
                                    style="font-weight: bold;"
                                    data-key="t-ecommerce"
                                >
                                    events đã xóa
                                </a>
                            </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            
        </div> 


    </div>
   </div>


@endsection

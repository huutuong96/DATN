@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
        <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách event đang hoạt động</h4>
                          <!-- Toggle Between Modals -->
                     <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                     data-bs-target="#firstmodal">Thêm Sự kiện</button>
                 <!-- First modal dialog -->
                 <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..."
                     tabindex="-1">
                     <div class="modal-dialog modal-dialog-centered modal-xl">
                         <div class="modal-content">
                             <div class="modal-body text-center p-5">
                                <form id="addBlogForm" 
                                action="{{ route('store_events', ['token' => auth()->user()->refesh_token]) }}" 
                                method="POST">
                              @csrf
                              <div class="row g-3">
                                  <div class="col-md-6">
                                      <label for="event_title" class="form-label">Tiêu đề sự kiện</label>
                                      <input type="text" class="form-control" id="event_title" name="event_title" value="{{ old('event_title') }}" required>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="event_day" class="form-label">Ngày</label>
                                      <input type="number" class="form-control" id="event_day" name="event_day" min="1" max="31" value="{{ old('event_day') }}" required>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="event_month" class="form-label">Tháng</label>
                                      <input type="number" class="form-control" id="event_month" name="event_month" min="1" max="12" value="{{ old('event_month') }}" required>
                                  </div>
                                  <div class="col-md-2">
                                      <label for="event_year" class="form-label">Năm</label>
                                      <input type="number" class="form-control" id="event_year" name="event_year" min="1900" max="2100" value="{{ old('event_year') }}" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="qualifier" class="form-label">Điều kiện</label>
                                      <input type="text" class="form-control" id="qualifier" name="qualifier" value="{{ old('qualifier') }}">
                                  </div>
                                  <div class="col-md-6">
                                      <label for="voucher_apply" class="form-label">Tiêu Đề Mã Giảm Giá</label>
                                      <input type="text" class="form-control" id="voucher_apply" name="voucher_apply" value="{{ old('voucher_apply') }}">
                                  </div>
                                  <div class="col-md-4">
                                      <label for="voucher_apply" class="form-label">Mô Tả Cho Giảm Giá</label>
                                      <input type="text" class="form-control" id="voucher_apply" name="voucher_description" value="{{ old('voucher_apply') }}">
                                  </div>
                                  <div class="col-md-2">
                                      <label for="voucher_apply" class="form-label">Số lượng mã giảm giá</label>
                                      <input type="text" class="form-control" id="voucher_apply" name="voucher_quantity" value="{{ old('voucher_apply') }}">
                                  </div>
                                  <div class="col-md-2">
                                      <label for="voucher_apply" class="form-label">Số tiền giới hạn</label>
                                      <input type="text" class="form-control" id="voucher_apply" name="voucher_limit" value="{{ old('voucher_apply') }}">
                                  </div>
                                  <div class="col-md-2">
                                      <label for="voucher_apply" class="form-label">% Giảm giá</label>
                                      <input type="text" class="form-control" id="voucher_apply" name="voucher_ratio" value="{{ old('voucher_apply') }}">
                                  </div>
                                  <div class="col-md-2">
                                      <label for="voucher_apply" class="form-label">Mã Giảm Giá</label>
                                      <input type="text" class="form-control" id="voucher_apply" name="voucher_code" value="{{ old('voucher_apply') }}" required>
                                  </div>
                                  <div class="col-md-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_share_facebook" name="is_share_facebook" value="1" {{ old('is_share_facebook') ? 'checked' : '' }}>
                                        <label for="is_share_facebook" class="form-check-label">Chia sẻ Facebook</label>
                                    </div>
                                    <div class="form-check mt-4">
                                      <input type="checkbox" class="form-check-input" id="is_share_zalo" name="is_share_zalo" value="1" {{ old('is_share_zalo') ? 'checked' : '' }}>
                                      <label for="is_share_zalo" class="form-check-label">Chia sẻ Zalo</label>
                                  </div>
                                  <div class="form-check mt-4">
                                      <input type="checkbox" class="form-check-input" id="is_mail" name="is_mail" value="1" {{ old('is_mail') ? 'checked' : '' }}>
                                      <label for="is_mail" class="form-check-label">Gửi email</label>
                                  </div>
                                </div>
                                  <div class="col-md-3">
                                      <label for="point" class="form-label">Áp dụng theo điểm</label>
                                      <input type="number" class="form-control" id="point" name="point" value="{{ old('point', 0) }}">
                                  </div>
                                
                                  <div class="col-md-3">
                                      <label for="where_order" class="form-label">Áp dụng theo số lượng đơn đã đặt</label>
                                      <input type="number" class="form-control" id="where_order" name="where_order" value="{{ old('where_order', 0) }}">
                                  </div>
                                  <div class="col-md-3">
                                      <label for="where_price" class="form-label">Áp dụng theo tổng tiền đặt hàng</label>
                                      <input type="number" step="0.01" class="form-control" id="where_price" name="where_price" value="{{ old('where_price', 0) }}">
                                  </div>
                                  <!-- <div class="col-md-6">
                                      <label for="date" class="form-label">Ngày</label>
                                      <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}">
                                  </div> -->
                                  <div class="col-md-3">
                                      <label for="from" class="form-label">Áp Dụng Từ Ngày</label>
                                      <input type="date" class="form-control" id="from" name="from" value="{{ old('from') }}">
                                  </div>
                                  <div class="col-md-3">
                                      <label for="to" class="form-label">Áp Dụng Đến Hết Ngày</label>
                                      <input type="date" class="form-control" id="to" name="to" value="{{ old('to') }}">
                                  </div>
                                  <div class="col-md-6">
                                      <label for="status" class="form-label">Trạng thái</label>
                                      <select style="width: 507px;left: 2rem; margin-top: 1px " style="width: 130px" class="form-control" id="status" name="status" required>
                                          <option value="" disabled selected>Chọn trạng thái</option>
                                          <option value="2">Hoạt động</option>
                                          <option value="3">Không hoạt động</option>
                                      </select>
                                  </div>
                                  <div class="col-md-12">
                                      <label for="description" class="form-label">Mô tả</label>
                                      <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                  </div>
                                  <div class="col-md-12 ">
                                      <button type="submit" class="btn btn-primary mt-3">Thêm sự kiện</button>
                                  </div>
                              </div>
                          </form>
                          

                                 
                                 
                             </div>
                         </div>
                     </div>
                 </div>
                    </div><!-- end card header -->
                   
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Tên event</th>
                                            <th scope="col">Đk áp dụng</th>
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
                                                      <div>
                                                            @if ($event->status == 2)
                                                                
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
                                                            
                                                            @elseif ($event->status == 1)
                                                                
                                                                <a 
                                                                    href="{{ route('change_status_events', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $event->id,
                                                                                                        'status' => 2,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-success" title="mở"> <i class="ri-check-line align-middle"></i></button>

                                                             
                                                            @endif
                                                            
                                                                <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal{{ $event->id }}" title="Chỉnh sửa"><i class="ri-edit-line align-middle"></i></button>
                                                                <!-- First modal dialog -->
                                                                <div class="modal fade" id="firstmodal{{ $event->id }}" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                                                    <div class="modal-dialog modal-dialog-centered" style=" margin-left: 20%;">
                                                                        <div class="modal-content" style="width:1000px">
                                                                            <div class="modal-body text-center p-5" style="width:1000px">
                                                                            <form id="addBlogForm"
                                                                                                action="{{ route('update_events', [
                                                                                                    'token' => auth()->user()->refesh_token,'id'=>$event->id
                                                                                                ]) }}"
                                                                                                method="POST" >
                                                                                @csrf
                                                                                <input type="hidden" name="_method" value="PUT">
                                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                                                                <div class="row g-3">
                                                                                    <div class="col-md-6">
                                                                                        <label for="event_title" class="form-label">Tiêu đề</label>
                                                                                        <input type="text" class="form-control" id="event_title" name="event_title" value="{{ old('event_title', $event->event_title ?? '') }}" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="event_day" class="form-label">Ngày</label>
                                                                                        <input type="number" class="form-control" id="event_day" name="event_day" min="1" max="31" value="{{ old('event_day', $event->event_day ?? '') }}" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="event_month" class="form-label">Tháng</label>
                                                                                        <input type="number" class="form-control" id="event_month" name="event_month" min="1" max="12" value="{{ old('event_month', $event->event_month ?? '') }}" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="event_year" class="form-label">Năm</label>
                                                                                        <input type="number" class="form-control" id="event_year" name="event_year" min="1900" max="2100" value="{{ old('event_year', $event->event_year ?? '') }}" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="qualifier" class="form-label">Điều kiện</label>
                                                                                        <input type="text" class="form-control" id="qualifier" name="qualifier" value="{{ old('qualifier', $event->qualifier ?? '') }}">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="voucher_apply" class="form-label">Áp dụng voucher</label>
                                                                                        <input type="text" class="form-control" id="voucher_apply" name="voucher_apply" value="{{ old('voucher_apply', $event->voucher_apply ?? '') }}">
                                                                                    </div>
                                                                                
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-check mt-4">
                                                                                            <input type="checkbox" class="form-check-input" id="is_share_facebook" name="is_share_facebook" value="1" {{ old('is_share_facebook', $event->is_share_facebook ?? 0) ? 'checked' : '' }}>
                                                                                            <label for="is_share_facebook" class="form-check-label">Chia sẻ Facebook</label>
                                                                                        </div>
                                                                                        <div class="form-check mt-4">
                                                                                            <input type="checkbox" class="form-check-input" id="is_share_zalo" name="is_share_zalo" value="1" {{ old('is_share_zalo', $event->is_share_zalo ?? 0) ? 'checked' : '' }}>
                                                                                            <label for="is_share_zalo" class="form-check-label">Chia sẻ Zalo</label>
                                                                                        </div>
                                                                                        <div class="form-check mt-4">
                                                                                            <input type="checkbox" class="form-check-input" id="is_mail" name="is_mail" value="1" {{ old('is_mail', $event->is_mail ?? 0) ? 'checked' : '' }}>
                                                                                            <label for="is_mail" class="form-check-label">Gửi email</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="point" class="form-label">Điểm</label>
                                                                                        <input type="number" class="form-control" id="point" name="point" value="{{ old('point', $event->point ?? 0) }}">
                                                                                    </div>
                                                                                    
                
                                                                                    <div class="col-md-6">
                                                                                        <label for="where_order" class="form-label">Vị trí đơn hàng</label>
                                                                                        <input type="number" class="form-control" id="where_order" name="where_order" value="{{ old('where_order', $event->where_order ?? 0) }}">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="where_price" class="form-label">Giá đơn hàng</label>
                                                                                        <input type="number" step="0.01" class="form-control" id="where_price" name="where_price" value="{{ old('where_price', $event->where_price ?? 0) }}">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="date" class="form-label">Ngày</label>
                                                                                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $event->date ?? '') }}">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="from" class="form-label">Thời gian bắt đầu</label>
                                                                                        <input type="datetime-local" class="form-control" id="from" name="from" value="{{ old('from', $event->from ?? '') }}">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="to" class="form-label">Thời gian kết thúc</label>
                                                                                        <input type="datetime-local" class="form-control" id="to" name="to" value="{{ old('to', $event->to ?? '') }}">
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <label for="status" class="form-label">Trạng thái</label>
                                                                                        <select 
                                                                                            style="margin-top: 2px; margin-left: -30px; width: 50%;" 
                                                                                            class="form-select" 
                                                                                            id="status" 
                                                                                            name="status"
                                                                                        >
                                                                                            <option value="1" {{ $event->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                                                                                            <option value="2" {{ $event->status == 2 ? 'selected' : '' }}>Không hoạt động</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    
                                                                                    <div class="col-md-12">
                                                                                        <label for="description" class="form-label">Mô tả</label>
                                                                                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $event->description ?? '') }}</textarea>
                                                                                    </div>

                                                                                    <div class="col-md-12 text-center">
                                                                                        <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                            </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div> 
                                                            <div class="mt-2">
                                                                <a 
                                                                    href="{{ route('change_status_events', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $event->id,
                                                                                                        'status' => 5,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-danger" title="xóa"> <i class=" ri-close-line align-middle"></i></button>
                                        
                                                               
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#eventDetailsModal-{{ $event->id }}">
                                                                    <button type="button" class="btn btn-primary" title="Chi tiết sự kiện">
                                                                        <i class="ri-eye-line align-middle"></i>
                                                                    </button>
                                                                </a>
                                                                <div class="modal fade" id="eventDetailsModal-{{ $event->id }}" tabindex="-1" aria-labelledby="eventDetailsModalLabel-{{ $event->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="eventDetailsModalLabel-{{ $event->id }}">Chi tiết Sự kiện</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <h4><strong>Tiêu đề:</strong> {{ $event->event_title }}</h4>
                                                                                        <p><strong>Ngày:</strong> {{ $event->event_day }}/{{ $event->event_month }}/{{ $event->event_year }}</p>
                                                                                        <p><strong>Trạng thái:</strong> {{ $event->status == 1 ? 'Hoạt động' : 'Không hoạt động' }}</p>
                                                                                        <p><strong>Mô tả:</strong></p>
                                                                                        <p><strong>Điều kiện:</strong> {{ $event->qualifier }}</p>
                                                                                        <p><strong>Áp dụng voucher:</strong> {{ $event->voucher_apply }}</p>
                                                                                        <p><strong>Điểm:</strong> {{ $event->point }}</p>
                                                                                        <div>{!! $event->description !!}</div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                       
                                                                                        <p><strong>Vị trí đơn hàng:</strong> {{ $event->where_order }}</p>
                                                                                        <p><strong>Giá đơn hàng:</strong> {{ $event->where_price }}</p>
                                                                                        <p><strong>Ngày:</strong> {{ $event->date }}</p>
                                                                                        <p><strong>Thời gian bắt đầu:</strong> {{ $event->from }}</p>
                                                                                        <p><strong>Thời gian kết thúc:</strong> {{ $event->to }}</p>
                                                                                        <p><strong>Chia sẻ Facebook:</strong> {{ $event->is_share_facebook ? 'Có' : 'Không' }}</p>
                                                                                        <p><strong>Chia sẻ Zalo:</strong> {{ $event->is_share_zalo ? 'Có' : 'Không' }}</p>
                                                                                        <p><strong>Gửi email:</strong> {{ $event->is_mail ? 'Có' : 'Không' }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


  
                                                        
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
                                    href="{{ route('trash_events',['token' => auth()->user()->refesh_token]) }}"
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

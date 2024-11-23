@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
        <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách cửa hàng đã xóa</h4>
                        <a
                            href="{{ route('store', ['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            Danh sách cửa hàng
                        </a> 
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Tên cửa hàng</th>
                                            <th scope="col">Thông tin chủ shop</th>
                                            <th scope="col">Địa chỉ</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Doanh thu trong tháng</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shops->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có cửa hàng nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($shops as $shop)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $shop->id }}</a></th>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $shop->shop_name ?? "Chưa đặt tên"}}
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal;">
                                                        <img src="{{$shop->user[0]->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="Avatar" class="avatar-xs rounded-circle me-3 material-shadow" style="width: 60px; height: 60px;">
                                                        <div style="display: flex; flex-direction: column;">
                                                            <span style="font-weight: bold;">{{$shop->user[0]->fullname ?? 'No Name'}}</span>
                                                            <span style="color: gray;">{{$shop->user[0]->phone ?? 'No Phone'}}</span>
                                                            <span style="color: gray;">{{$shop->user[0]->phone ?? 'No Phone'}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        -{{ $shop->district }} <br>
                                                        -{{ $shop->ward }} <br>
                                                        -{{ $shop->pick_up_address }} <br>
                                                    </td>
                                                    <td>{{ $shop->created_at}}</td>
                                                    <td>
                                                        {{number_format($shop->doanhthu)}} vnđ
                                                    <td>
                                                    <ul class="list-inline">
                                                        <li class="list-inline-item">
                                                            <a 
                                                                href="{{ route('change_shop', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $shop->id,
                                                                                                    'status' => 2,
                                                                                                    ]) }}"
                                                            >
                                                            <button type="button" class="btn btn-info"
                                                            title="Khôi phục"
                                                            onclick="return confirm('Bạn có chắc chắn muốn khôi phục user này?');">
                                                            <i class="ri-refresh-line align-middle"></i>
                                                        </button>
                                                        </li>
                                                        <li class="mt-2 mb-2">
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $shop->id }}">
                                                                <button type="button" class="btn btn-primary" title="Chi tiết sản phẩm">
                                                                    <i class="ri-eye-line align-middle"></i>
                                                                </button>
                                                            </a>
                                                        
                                                            <!-- Modal Chi tiết sản phẩm -->
                                                            <div class="modal fade" id="detailsModal-{{ $shop->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $shop->id }}" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="detailsModalLabel-{{ $shop->id }}">Chi tiết Sản phẩm</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                        <div class="card shadow-sm">
                                                                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                                    <h5 class="mb-0 text-white">Thông Tin Shop</h5>
                                                                                    <span class="badge bg-success">Đã bị xóa</span>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="">
                                                                                        <img src="{{$shop->user[0]->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="Avatar" class="rounded-circle me-3 mb-3" style="width: 100px; height: 100px;">
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <!-- Cột trái -->
                                                                                        <div class="col-lg-6">
                                                                                            <div class="mb-3">
                                                                                                <strong>ID:</strong> <span class="text-muted">{{$shop->id}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Tên Shop:</strong> <span class="text-muted">{{$shop->shop_name}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Mô Tả:</strong> <span class="text-muted">{{$shop->description}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Mã Shop:</strong> <span class="text-muted">{{$shop->slug}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Số Điện Thoại:</strong> <span class="text-muted">{{$shop->contact_number ?? "chưa nhập"}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Người Đại Diện:</strong> <span class="text-muted">{{$shop->user[0]->fullname ?? "chưa nhập"}}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Cột phải -->
                                                                                        <div class="col-lg-6 mb-3">
                                                                                            <div class="mb-3">
                                                                                                <strong>Tỉnh/Thành:</strong> <span class="text-muted">{{$shop->province ?? "chưa nhập"}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Quận/Huyện:</strong> <span class="text-muted">{{$shop->district ?? "chưa nhập"}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Xã/Phường:</strong> <span class="text-muted">{{$shop->ward ?? "chưa nhập"}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Ngày Tạo:</strong> <span class="text-muted">{{$shop->created_at}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Ngày Cập Nhật:</strong> <span class="text-muted">{{$shop->updated_at}}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                    {{ $shops->appends(['token' => auth()->user()->refesh_token])->links() }}
                                </div>
                            </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            
        </div> 


    </div>
   </div>


@endsection

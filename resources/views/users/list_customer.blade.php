@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
        <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách tài khoản khách hàng</h4>
                        <a
                            href="{{ route('costomer', ['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            Danh sách Khách hàng
                        </a>
                        <div style="width: 20px;">/</div> 
                        <a
                            href="{{ route('manager',['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            Danh sách user quản lý
                        </a>

                        <div style="width: 20px;">/</div> 
                        <a
                            href="{{ route('trash_user',['token' => auth()->user()->refesh_token]) }}"
                            class="nav-link text-primary"
                            style="font-weight: bold;"
                            data-key="t-ecommerce"
                        >
                            User đã xóa
                        </a>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <<table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Ảnh đại diện</th>
                                            <th scope="col">Thông tin tài khoản</th> 
                                            <th scope="col">Ngày tạo</th>  
                                            <th scope="col ">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($users->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có tài khoản khách hàng nào.</td>
                                            </tr>
                                        @else
                                            @foreach($users as $user)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $user->id }}</a></th>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        <img src="{{$user->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="Avatar" class="avatar-xs rounded-circle me-3 material-shadow" style="width: 60px; height: 60px;">
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        <div style="display: flex; flex-direction: column;">
                                                            <span style="font-weight: bold;">{{$user->fullname ?? 'No Name'}}</span>
                                                        </div>
                                                    </td>
                                                    
                                                    <td>{{ $user->created_at}}</td>
                                                    
                                                    <td>
                                                    <ul class="list-inline">
                                                        @if ($user->status == 1)
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_user', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $user->id,
                                                                                                        'status' => 2,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-warning" title="Khóa">
                                                                    <i class="ri-lock-line align-middle"></i>
                                                                </button>                                                                </a>
                                                            </li>
                                                        @elseif ($user->status == 2)
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_user', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $user->id,
                                                                                                        'status' => 1,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-success" title="mở"> <i class="ri-check-line align-middle"></i></button>
                                                            </li>
                                                        @endif
                                                        <li class="list-inline-item">
                                                            <a 
                                                                href="{{ route('change_user', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'status' => 4,
                                                                                                    ]) }}"
                                                            >
                                                            <button type="button" class="btn btn-warning" title="vi pham"> <i class=" ri-close-line align-middle"></i></button>
                                                            </li>   
                                                        
                                                    </ul>
                                                    <ul class="list-inline d-flex">
                                                        <li class="list-inline-item me-2">
                                                            <a 
                                                                    href="{{ route('change_user', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $user->id,
                                                                                                        'status' => 5,
                                                                                                        ]) }}"
                                                            >
                                                                <button type="button" class="btn btn-danger" title="Xóa"  onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');">
                                                                    <i class="ri-delete-bin-line align-middle"></i>
                                                                </button>
                                                            </a>
                                                        </li>
                                                        <li class="ms-1">
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $user->id }}">
                                                                <button type="button" class="btn btn-primary" title="Thông tin chi tiết tài khoản">
                                                                    <i class="ri-eye-line align-middle"></i>
                                                                </button>
                                                            </a>
                                                        
                                                            <!-- Modal Chi tiết sản phẩm -->
                                                            <div class="modal fade" id="detailsModal-{{ $user->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $user->id }}" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="detailsModalLabel-{{ $user->id }}">Thông tin tài khoản</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="card shadow-sm">
                                                                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                                    <h5 class="mb-0 text-white">Thông Tin Tài Khoản</h5>
                                                                                    <span class="badge bg-success">Hoạt Động</span>
                                                                                </div>
                                                                                <div class="card-body">
                                                                                    <div class="row">
                                                                                        <div class="">
                                                                                            <img src="{{$user->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="Avatar" class="rounded-circle me-3 mb-3" style="width: 100px; height: 100px;">
                                                                                        </div>
                                                                                        <!-- Cột trái -->
                                                                                        <div class="col-lg-6">
                                                                                            <div class="mb-3 d-flex ">
                                                                                                <strong>ID:</strong> <span class="text-muted me-5">{{ $user->id }}</span>
                                                                                                <strong>CHỨC VỤ: </strong> <span class="text-muted">{{ $user->role->title}}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Tên Tài Khoản:</strong> <span class="text-muted">{{ $user->fullname }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Số Điện Thoại:</strong> <span class="text-muted">{{ $user->phone }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Email:</strong> <span class="text-muted">{{ $user->email }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Giới Tính:</strong> <span class="text-muted">{{ $user->genre }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Ngày Sinh Nhật:</strong> <span class="text-muted">{{ $user->datebirth }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Cột phải -->
                                                                                        <div class="col-lg-6">
                                                                                            <div class="mb-3">
                                                                                                <strong>Mô Tả:</strong> <span class="text-muted">{{ $user->description }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Thành Viên Hạng:</strong> <span class="text-muted">{{ $user->rank->title ?? "Không hạng" }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Điểm Tích Lũy:</strong> <span class="text-muted">{{ $user->point }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Ngày Tạo:</strong> <span class="text-muted">{{ $user->created_at }}</span>
                                                                                            </div>
                                                                                            <div class="mb-3">
                                                                                                <strong>Ngày Cập Nhật:</strong> <span class="text-muted">{{ $user->updated_at }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Danh Sách Địa Chỉ -->
                                                                            <div class="mt-5">
                                                                                <h4 class="mb-3">Danh Sách Địa Chỉ</h4>
                                                                                <table class="table table-bordered table-hover shadow-sm">
                                                                                    <thead class="table-light">
                                                                                        <tr>
                                                                                            <th>#</th>
                                                                                            <th>Phân Loại</th>
                                                                                            <th>Tỉnh/Thành</th>
                                                                                            <th>Quận/Huyện</th>
                                                                                            <th>Xã/Phường</th>
                                                                                            <th>Người Nhận</th>
                                                                                            <th>Số Điện Thoại</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach($user->address as $data)
                                                                                        <tr>
                                                                                            <td>{{$data->id}}</td>
                                                                                            <td>{{$data->type}}</td>
                                                                                            <td>{{$data->province}}</td>
                                                                                            <td>{{$data->district}}</td>
                                                                                            <td>{{$data->ward}}</td>
                                                                                            <td>{{$data->name}}</td>
                                                                                            <td>{{$data->phone}}</td>
                                                                                        </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
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
                                    {{ $users->appends(['token' => auth()->user()->refesh_token])->links() }}
                                </div>
                                <a
                                    href="{{ route('trash_user',['token' => auth()->user()->refesh_token]) }}"
                                    class="nav-link text-primary"
                                    style="font-weight: bold;"
                                    data-key="t-ecommerce"
                                >
                                    User đã xóa
                                </a>
                            </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
        </div> 
    </div>
   </div>


@endsection

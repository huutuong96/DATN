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
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Ảnh đại diện</th>
                                            <th scope="col">Thông tin tài khoản</th>
                                            <th scope="col">Địa chỉ</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Mức rank và tích điểm</th>
                                            <th scope="col">Hành động</th>
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
                                                            <span style="color: gray;">{{$user->email ?? 'No Email'}}</span>
                                                            <span style="color: gray;">{{$user->phone ?? 'No Phone'}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @foreach($user->address as $address)
                                                            *. {{ $address->district }}_{{ $address->ward }}_{{ $address->address }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $user->created_at}}</td>
                                                    <td>
                                                        {{$user->rank->title ?? "Vô danh"}}: {{$user->point}} điểm tích lũy
                                                    <td>
                                                    <ul class="list-inline">
                                                        @if ($user->status == 1)
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_user', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $user->id,
                                                                                                        'status' => 4,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-warning" title="Khóa">
                                                                    <i class="ri-lock-line align-middle"></i>
                                                                </button>
                                                                
                                                                </a>
                                                            </li>
                                                        @elseif ($user->status == 2)
                                                            <li class="list-inline-item">
                                                            <a 
                                                                href="{{ route('change_user', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'status' => 2,
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
                                                                                                        'status' => 5,
                                                                                                        ]) }}"
                                                            >
                                                            <button type="button" class="btn btn-danger" title="Xóa"  onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');">
                                                                <i class="ri-delete-bin-line align-middle"></i>
                                                        </button>
                                                            </a>
                                                        </li>
                                                        <li class="mt-2 mb-2">
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $user->id }}">
                                                                <button type="button" class="btn btn-primary" title="Thông tin tài khoản">
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
                                                                        <div class="container my-5">
                                                                                <!-- Card Thông tin chi tiết -->
                                                                                <div class="card">
                                                                                    <div class="card-header bg-info text-white d-flex align-items-center">
                                                                                        <img src="https://via.placeholder.com/50" alt="Avatar" class="rounded-circle me-3">
                                                                                        <h5 class="mb-0">Thông Tin Người Dùng</h5>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <!-- Tên -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Tên:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">Nguyễn Văn A</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Số điện thoại -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Số Điện Thoại:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">0987 654 321</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Email -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Email:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">nguyenvana@example.com</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Mô tả -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Mô Tả:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">Thành viên VIP của hệ thống.</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Điểm -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Điểm:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">1500</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Giới tính -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Giới Tính:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">Nam</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Ngày sinh -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Ngày Sinh:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">01/01/1990</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Hạng -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Hạng:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">Gold</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Vai trò -->
                                                                                        <div class="row mb-3">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Vai Trò:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext">Quản Trị Viên</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- Trạng thái -->
                                                                                        <div class="row">
                                                                                            <label class="col-sm-4 col-form-label fw-bold">Trạng Thái:</label>
                                                                                            <div class="col-sm-8">
                                                                                                <p class="form-control-plaintext text-success">Hoạt Động</p>
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

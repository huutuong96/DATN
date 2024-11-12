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
                                                    <td style="display: flex; align-items: center;">
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
                                                                    <button type="button" class="btn rounded-pill btn-warning waves-effect waves-light">Khóa</button>
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
                                                                <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">mở</button>
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
                                                                <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">Xóa</button>
                                                            </a>
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

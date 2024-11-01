@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
        <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm đã xóa</h4>
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
                                            <th scope="col">Doanh thuTtrong tháng</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shops->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($shops as $shop)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $shop->id }}</a></th>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $shop->shop_name ?? "Chưa đặt tên"}}
                                                    </td>
                                                    <td style="display: flex; align-items: center;">
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
                                                                <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">Khôi phục</button>
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

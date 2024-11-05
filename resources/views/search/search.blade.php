@extends('index')
@section('title', 'List Store')
@section('link')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
  
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
@endsection
@section('main')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách tìm kiếm</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="live-preview">
                        <div class="table-responsive">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{session('tab')  == 'products' ? 'active' : ''}}" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="{{session('tab')  == 'products' ? 'true' : 'false'}}">Sản phẩm</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{session('tab') == 'shops' ? 'active' : ''}}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="{{session('tab')  == 'shops' ? 'true' : 'false'}}">Cửa hàng</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{session('tab') == 'users' ? 'active' : ''}}" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="{{session('tab')  == 'users' ? 'true' : 'false'}}">Tài khoản</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{session('tab') == 'posts' ? 'active' : ''}}" id="blog-tab" data-bs-toggle="tab" data-bs-target="#blog-tab-pane" type="button" role="tab" aria-controls="blog-tab-pane" aria-selected="{{session('tab')  == 'posts' ? 'true' : 'false'}}">Bài viết</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade {{session('tab')  == 'products' ? 'show active' : ''}}" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                    <table id="product" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>Tên sảm phẩm</th>
                                                <th>SLUG và SKU</th>
                                                <th>Description</th>
                                                <th>Infomation</th>
                                                <th>Price</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resultsByTable["products"] as $data)
                                                <tr>
                                                    <td>{{$data->id}}</td>
                                                    <td><img src="{{$data->image}}" alt="{{$data->name}}" style="height: 100px; width: 100px" ></td>
                                                    <td>
                                                        {{$data->name}} <br>
                                                    @switch($data->status)
                                                        @case(1)
                                                            <span style="color: red;"><b>(Không hoạt động)</b></span>
                                                            @break

                                                        @case(2)
                                                            <span style="color: green;"><b>(Đang hoạt động)</b></span>
                                                            @break

                                                        @case(3)
                                                        @case(101)
                                                            <span style="color: orange;"><b>(Chờ duyệt)</b></span>
                                                            @break

                                                        @case(4)
                                                            <span style="color: purple;"><b>(Vi phạm)</b></span>
                                                            @break

                                                        @case(5)
                                                            <span style="color: gray;"><b>(Xóa mềm)</b></span>
                                                            @break

                                                        @case(0)
                                                            <span style="color: brown;"><b>(Không đồng ý khi duyệt)</b></span>
                                                            @break

                                                        @default
                                                            <span style="color: black;"><b>(Trạng thái không xác định)</b></span>
                                                    @endswitch
                                                    </td>
                                                    <td>
                                                        <b>SKU:</b> {{$data->sku}} 
                                                        <br> 
                                                        <b>SLUG:</b> {{$data->slug}}
                                                    </td>
                                                    <td>
                                                        {{ \Illuminate\Support\Str::limit($data->description, 100, '...') }}
                                                        <!-- {{$data->description}} -->
                                                    </td>
                                                    <td>
                                                        {{ \Illuminate\Support\Str::limit($data->infomation, 100, '...') }}
                                                        <!-- {{$data->infomation}} -->
                                                    </td>
                                                    <td>
                                                        <b>SHOW PRICE:</b> {{$data->show_price}} 
                                                        <br> 
                                                        <b>PRICE:</b> {{$data->price}}
                                                        <br> 
                                                        <b>SALE PRICE:</b> {{$data->sale_price}}
                                                    </td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' =>$data->id,
                                                                                                'tab'=>'products',
                                                                                                'search'=>$search
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                     
                                                        <!-- Báo cáo vi phạm -->
                                                        
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal">
                                                                <button type="submit" class="btn btn-danger" title="Báo cáo vi phạm">
                                                                    <i class="ri-error-warning-line align-middle"></i> 
                                                                </button>
                                                            </a>
                                                        
                        
                                                        <!-- Modal Báo cáo vi phạm -->
                                                        <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="reportModalLabel">Báo cáo vi phạm</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="{{ route('products.submitReport', [
                                                                            'id' =>$data->id,
                                                                            'token' => auth()->user()->refesh_token,
                                                                            'search'=>$search,
                                                                            'tab' => 'products'
                                                                        ]) }}" method="POST">
                                                                            @csrf
                                                                            <div class="mb-3">
                                                                                <label for="reason" class="form-label">Lý do vi phạm:</label>
                                                                                <textarea name="reason" id="reason" class="form-control" required></textarea>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                        
                                                    
                                                        <!-- Không duyệt -->
                                                        <form action="{{ route('products.reject',[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' =>$data->id,
                                                                                                'tab'=>'products',
                                                                                                'search'=>$search,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning" title="Không duyệt">
                                                                <i class="ri-close-circle-line align-middle"></i> 
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                    new DataTable('#product');
                                    </script>
                                </div>
                                <div class="tab-pane fade {{session('tab') == 'shops' ? 'show active' : ''}}" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                    <table id="shop" class="display" style="width:100%">
                                         <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Tên cửa hàng</th>
                                                <th scope="col">Thông tin chủ shop</th>
                                                <th scope="col">Địa chỉ</th>
                                                <th scope="col">Ngày tạo</th>
                                                <th scope="col">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resultsByTable["shops"] as $shop)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $shop->id }}</a></th>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $shop->shop_name ?? "Chưa đặt tên"}}-{{ $shop->status}}<br>
                                                        @switch($shop->status)
                                                            @case(1)
                                                                <span style="color: red;"><b>(Không hoạt động)</b></span>
                                                                @break

                                                            @case(2)
                                                                <span style="color: green;"><b>(Đang hoạt động)</b></span>
                                                                @break
                                                            @case(101)
                                                                <span style="color: orange;"><b>(Chờ duyệt)</b></span>
                                                                @break

                                                            @case(4)
                                                                <span style="color: purple;"><b>(Vi phạm)</b></span>
                                                                @break

                                                            @case(5)
                                                                <span style="color: gray;"><b>(Xóa mềm)</b></span>
                                                                @break

                                                            @case(0)
                                                                <span style="color: brown;"><b>(Không đồng ý khi duyệt)</b></span>
                                                                @break

                                                            @default
                                                                <span style="color: black;"><b>(Trạng thái không xác định)</b></span>
                                                        @endswitch
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
                                                        <ul class="list-inline">
                                                            @if($shop->status == 2)
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('changeShopSearch', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $shop->id,
                                                                                                        'tab' => 'shops',
                                                                                                        'search'=>$search,
                                                                                                        'status' => 4,
                                                                                                        ]) }}"
                                                                >
                                                                    <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">khóa</button>
                                                                </a>
                                                            </li>
                                                            @elseif ($shop->status == 4)
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('changeShopSearch', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $shop->id,
                                                                                                        'tab' => 'shops',
                                                                                                        'search'=>$search,
                                                                                                        'status' => 2,
                                                                                                        ]) }}"
                                                                >
                                                                    <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">mở</button>
                                                                </a>
                                                            </li>
                                                            @endif
                                                            @if ($shop->status == 5)
                                                            <li class="list-inline-item mt-2">
                                                                <a 
                                                                    href="{{ route('changeShopSearch', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $shop->id,
                                                                                                        'tab' => 'shops',
                                                                                                        'search'=>$search,
                                                                                                        'status' => 2,
                                                                                                        ]) }}"
                                                                >
                                                                    <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">Khôi phục</button>
                                                                    </a>
                                                            </li>
                                                            @else
                                                            <li class="list-inline-item mt-2">
                                                                <a 
                                                                    href="{{ route('changeShopSearch', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $shop->id,
                                                                                                        'tab' => 'shops',
                                                                                                        'search'=>$search,
                                                                                                        'status' => 5,
                                                                                                        ]) }}"
                                                                >
                                                                    <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">xóa .</button>
                                                                </a>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        new DataTable('#shop');
                                    </script>
                                </div>
                                <div class="tab-pane fade {{session('tab') == 'users' ? 'show active' : ''}}" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                                    <table id="usershow" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Ảnh đại diện</th>
                                            <th scope="col">Thông tin tài khoản</th>
                                            <th scope="col">Địa chỉ</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Mức rank và tích điểm</th>
                                            <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resultsByTable["users"] as $user)
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-medium">{{ $user->id }}</a></th>
                                                <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                    <img src="{{$user->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="Avatar" class="avatar-xs rounded-circle me-3 material-shadow" style="width: 60px; height: 60px;">
                                                </td>
                                                <td style="display: flex; align-items: center;">
                                                    <div style="display: flex; flex-direction: column;">
                                                        <span style="font-weight: bold;">{{$user->fullname ?? 'No Name'}}</span>
                                                        <span style="color: gray;">{{$user->email ?? 'No Email'}}</span>
                                                        <span style="color: gray;">{{$user->phone ?? 'No Phone'}}</span><br>
                                                        @switch($user->status)
                                                            @case(1)
                                                                <span style="color: red;"><b>(Không hoạt động)</b></span>
                                                                @break

                                                            @case(2)
                                                                <span style="color: green;"><b>(Đang hoạt động)</b></span>
                                                                @break

                                                            @case(3)
                                                            @case(101)
                                                                <span style="color: orange;"><b>(Chờ duyệt)</b></span>
                                                                @break

                                                            @case(4)
                                                                <span style="color: purple;"><b>(Vi phạm)</b></span>
                                                                @break

                                                            @case(5)
                                                                <span style="color: gray;"><b>(Xóa mềm)</b></span>
                                                                @break

                                                            @case(0)
                                                                <span style="color: brown;"><b>(Không đồng ý khi duyệt)</b></span>
                                                                @break

                                                            @default
                                                                <span style="color: black;"><b>(Trạng thái không xác định)</b></span>
                                                        @endswitch
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
                                                </td>
                                                <td>
                                                    <ul class="list-inline">
                                                        @if($user->status == 2)
                                                        <li class="list-inline-item">
                                                            <a 
                                                                href="{{ route('changeUserSearch', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'tab' => 'users',
                                                                                                    'search'=>$search,
                                                                                                    'status' => 4,
                                                                                                    ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">khóa</button>
                                                            </a>
                                                        </li>
                                                        @elseif($user->status == 4)
                                                        <li class="list-inline-item">
                                                            <a 
                                                                href="{{ route('changeUserSearch', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'tab' => 'users',
                                                                                                    'search'=>$search,
                                                                                                    'status' => 2,
                                                                                                    ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">Mở</button>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if($user->status == 2)
                                                        <li class="list-inline-item mt-2">
                                                            <a 
                                                                href="{{ route('changeUserSearch', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'tab' => 'users',
                                                                                                    'search'=>$search,
                                                                                                    'status' => 5,
                                                                                                    ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">xóa .</button>
                                                            </a>
                                                        </li>
                                                        @elseif($user->status == 2)
                                                        <li class="list-inline-item mt-2">
                                                            <a 
                                                                href="{{ route('changeUserSearch', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'tab' => 'users',
                                                                                                    'search'=>$search,
                                                                                                    'status' => 5,
                                                                                                    ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">xóa .</button>
                                                            </a>
                                                        </li>
                                                        @else
                                                        <li class="list-inline-item mt-2">
                                                            <a 
                                                                href="{{ route('changeUserSearch', [
                                                                                                    'token' => auth()->user()->refesh_token,
                                                                                                    'id' => $user->id,
                                                                                                    'tab' => 'users',
                                                                                                    'search'=>$search,
                                                                                                    'status' => 5,
                                                                                                    ]) }}"
                                                            >
                                                                <button type="button" class="btn rounded-pill btn-warning waves-effect waves-light">Đang không hoạt động</button>
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                    <script>
                                    new DataTable('#usershow');
                                    </script>
                                </div>
                                <div class="tab-pane fade {{session('tab') == 'posts' ? 'show active' : ''}}" id="blog-tab-pane" role="tabpanel" aria-labelledby="blog-tab" tabindex="0">
                                    <table id="blog" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">blog</th>
                                                <th scope="col">slug</th>
                                                <th scope="col">tiêu đề</th>
                                                <th scope="col">nội dung</th>
                                                <th scope="col">Người tạo</th>
                                                <th scope="col">Hành động</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resultsByTable["posts"] as $Post)
                                            
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-medium">{{$Post->id}}</a></th>
                                                    <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->blog->name ?? "Danh mục đã bị xóa" }}<br>
                                                    @switch($data->status)
                                                        @case(1)
                                                            <span style="color: red;"><b>(Không hoạt động)</b></span>
                                                            @break

                                                        @case(2)
                                                            <span style="color: green;"><b>(Đang hoạt động)</b></span>
                                                            @break

                                                        @case(3)
                                                        @case(101)
                                                            <span style="color: orange;"><b>(Chờ duyệt)</b></span>
                                                            @break

                                                        @case(4)
                                                            <span style="color: purple;"><b>(Vi phạm)</b></span>
                                                            @break

                                                        @case(5)
                                                            <span style="color: gray;"><b>(Xóa mềm)</b></span>
                                                            @break

                                                        @case(0)
                                                            <span style="color: brown;"><b>(Không đồng ý khi duyệt)</b></span>
                                                            @break

                                                        @default
                                                            <span style="color: black;"><b>(Trạng thái không xác định)</b></span>
                                                    @endswitch</td>
                                                    <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->slug }}</td>
                                                    <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->title }}</td>
                                                    <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ \Illuminate\Support\Str::limit($Post->content, 150, '...') }}
                                                    </td>
                                                    <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->user->fullname }}</td>
                                                    <td>
                                                        <ul class="list-inline">
                                                            <li class="list-inline-item">
                                                                <a 
                                                                    href="{{ route('change_shop', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $data->id,
                                                                                                        'tab' => 'blogs',
                                                                                                        'search'=>$search,
                                                                                                        'status' => 4,
                                                                                                        ]) }}"
                                                                >
                                                                    <button type="button" class="btn rounded-pill btn-success waves-effect waves-light">khóa</button>
                                                            </li>
                                                            <li class="list-inline-item mt-2">
                                                                <a 
                                                                    href="{{ route('change_shop', [
                                                                                                        'token' => auth()->user()->refesh_token,
                                                                                                        'id' => $data->id,
                                                                                                        'tab' => 'blogs',
                                                                                                        'search'=>$search,
                                                                                                        'status' => 5,
                                                                                                        ]) }}"
                                                                >
                                                                <button type="button" class="btn rounded-pill btn-danger waves-effect waves-light">xóa .</button>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            @endforeach    
                                        </tbody>
                                        
                                    </table>
                                    <script>
                                    new DataTable('#blog');
                                    </script>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div><!-- end card -->
            </div> 
        </div>
    </div>
</div>


@endsection

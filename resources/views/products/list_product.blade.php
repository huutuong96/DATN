@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    @php 
    $index=5;
        
    @endphp
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 1 ? 'active' : '' }}" id="home-tab" data-bs-toggle="tab" data-bs-target="#all-products" type="button" role="tab" aria-controls="all-products" aria-selected="{{ $tab == 1 ? 'true' : 'false' }}">Tất cả({{$allProductsCount}})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 2 ? 'active' : '' }}" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-products" type="button" role="tab" aria-controls="pending-products" aria-selected="{{ $tab == 2 ? 'true' : 'false' }}">Chờ duyệt sản phẩm mới({{$newProductsCount}})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 6 ? 'active' : '' }}" id="pending-update-tab" data-bs-toggle="tab" data-bs-target="#pending-update-products" type="button" role="tab" aria-controls="pending-products" aria-selected="{{ $tab == 6 ? 'true' : 'false' }}">Chờ duyệt sản phẩm cập nhật({{$allUpdateProductsCount}})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 3 ? 'active' : '' }}" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-products" type="button" role="tab" aria-controls="active-products" aria-selected="{{ $tab == 3 ? 'true' : 'false' }}">Đang hoạt động({{$activeProductsCount}})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 4 ? 'active' : '' }}" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected-products" type="button" role="tab" aria-controls="rejected-products" aria-selected="{{ $tab == 4 ? 'true' : 'false' }}">Đã từ chối({{$rejectedProductsCount}})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 5 ? 'active' : '' }}" id="violating-tab" data-bs-toggle="tab" data-bs-target="#violating-products" type="button" role="tab" aria-controls="violating-products" aria-selected="{{ $tab == 5 ? 'true' : 'false' }}">Vi phạm({{$violatingProductsCount}})</button>
        </li>
    </ul>
    
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade {{ $tab == 1 ? 'show active' : '' }}" id="all-products" role="tabpanel" aria-labelledby="home-tab">
            <div class="col-xl-12">
                @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Tất cả sản phẩm</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($mergedProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($mergedProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $product->id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                        
                                                        {{-- 
                                                        @foreach ($product->images as $image)
                                                            <img src="{{ $image->url }}" alt="{{ $product->id }}" style="width: 30px; height: 30px; margin-top: 5px; margin-right: 5px;">
                                                        @endforeach --}}
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 3)
                                                        Chưa duyệt
                                                    @elseif($product->status == 2)
                                                        Đang hoạt động
                                                    @elseif($product->status == 5)
                                                        Đã từ chối
                                                    @elseif($product->status == 4)
                                                        Vi phạm
                             
                                                   
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{-- {{ $mergedProducts->links() }} <!-- Hiển thị liên kết phân trang --> --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
       
        <div class="tab-pane fade {{ $tab == 2 ? 'show active' : '' }}" id="pending-products" role="tabpanel" aria-labelledby="pending-tab">
            <div class="col-xl-12">
                @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm chờ duyệt</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($pendingProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($pendingProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $product->id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                        
                                                        {{-- 
                                                        @foreach ($product->images as $image)
                                                            <img src="{{ $image->url }}" alt="{{ $product->id }}" style="width: 30px; height: 30px; margin-top: 5px; margin-right: 5px;">
                                                        @endforeach --}}
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 3)
                                                        Chưa duyệt
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
                                                                                                'tab'=>2,
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
                                                                            'id' => $product->id,
                                                                            'token' => auth()->user()->refesh_token,
                                                                            'tab' => 2
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
                                                                                                'id' => $product->id,
                                                                                                'tab'=>2,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning" title="Không duyệt">
                                                                <i class="ri-close-circle-line align-middle"></i> 
                                                            </button>
                                                        </form>
                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{-- {{ $products->links() }} <!-- Hiển thị liên kết phân trang --> --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <div class="tab-pane fade {{ $tab == 6 ? 'show active' : '' }}" id="pending-update-products" role="tabpanel" aria-labelledby="pending-update-tab">
            <div class="col-xl-12">
                @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm chờ duyệt</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($allUpdateProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($allUpdateProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $product->id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                        
                                                        {{-- 
                                                        @foreach ($product->images as $image)
                                                            <img src="{{ $image->url }}" alt="{{ $product->id }}" style="width: 30px; height: 30px; margin-top: 5px; margin-right: 5px;">
                                                        @endforeach --}}
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 3)
                                                        Chưa duyệt
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
                                                                                                'tab'=>6,
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
                                                                            'id' => $product->id,
                                                                            'token' => auth()->user()->refesh_token,
                                                                            'tab' => 6
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
                                                                                                'id' => $product->id,
                                                                                                'tab'=>6,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning" title="Không duyệt">
                                                                <i class="ri-close-circle-line align-middle"></i> 
                                                            </button>
                                                        </form>
                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{-- {{ $products->links() }} <!-- Hiển thị liên kết phân trang --> --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
      
        <div class="tab-pane fade {{ $tab == 3 ? 'show active' : '' }}" id="active-products" role="tabpanel" aria-labelledby="active-tab">
            <div class="col-xl-12">
                @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm đang hoạt động</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($activeProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào hoạt động.</td>
                                            </tr>
                                        @else
                                            @foreach($activeProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $product->id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 2)
                                                       Đang hoạt động
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                   <!-- Nút Báo cáo vi phạm -->
                                                    <td>
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal">
                                                            <button type="submit" class="btn btn-danger" title="Báo cáo vi phạm">
                                                                <i class="ri-error-warning-line align-middle"></i> 
                                                            </button>
                                                        </a>
                                                    </td>

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
                                                                        'id' => $product->id,
                                                                        'token' => auth()->user()->refesh_token,
                                                                        'tab' => 3
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

                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{-- {{ $products->links() }} <!-- Hiển thị liên kết phân trang --> --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        
        <div class="tab-pane fade {{ $tab == 4 ? 'show active' : '' }}" id="rejected-products" role="tabpanel" aria-labelledby="rejected-tab">
            <div class="col-xl-12">
                @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm từ chối</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($rejectedProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào .</td>
                                            </tr>
                                        @else
                                            @foreach($rejectedProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $product->id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                        
                                                        {{-- 
                                                        @foreach ($product->images as $image)
                                                            <img src="{{ $image->url }}" alt="{{ $product->id }}" style="width: 30px; height: 30px; margin-top: 5px; margin-right: 5px;">
                                                        @endforeach --}}
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 5)
                                                        từ chối duyệt
                                                   
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
                                                                                                'tab'=>4,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                     
                                                      
                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{-- {{ $products->links() }} <!-- Hiển thị liên kết phân trang --> --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
       
        <div class="tab-pane fade {{ $tab == 5 ? 'show active' : '' }}" id="violating-products" role="tabpanel" aria-labelledby="violating-tab">
            <div class="col-xl-12">
                @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm vi phạm</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($violatingProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào </td>
                                            </tr>
                                        @else
                                            @foreach($violatingProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="#" class="fw-medium">{{ $product->id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                        
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 4)
                                                        sản phẩm vi phạm
                                                   
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
                                                                                                'tab'=>5,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                     
                                                      
                                                    </td>
                                                    
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Pagination Links -->
                        <div class="mt-3">
                            {{-- {{ $products->links() }} <!-- Hiển thị liên kết phân trang --> --}}
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        
    </div>
    

        

      </div>
   
   </div>
@endsection

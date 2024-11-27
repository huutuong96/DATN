@extends('index')
@section('title', 'List Store')
@section('link')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
  
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
@endsection

@section('main')
   <div class="container-fluid">
    
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
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Tất cả sản phẩm</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table id="all"  class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col-1">ID Sản phẩm</th>
                                            <th scope="col-3">Hình ảnh</th>
                                            <th scope="col-3">Tên sản phẩm</th>
                                            <th scope="col-3">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col-3">Tên Shop</th> 
                                            <th scope="col">Trạng thái</th>
                                            <th scope="col">Ngày tạo</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($mergedProducts->isEmpty())
                                            
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
                                                    <td>{{$product->shop->shop_name ?? "vô danh"}}</td> 
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
                            <script>
                                new DataTable('#all', {
                                    language: {   
                                        lengthMenu: "Hiển thị _MENU_ sản phẩm",
                                        search: "Tìm kiếm:" 
                                    }
                                });
                            </script>
                            
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
       
        <div class="tab-pane fade {{ $tab == 2 ? 'show active' : '' }}" id="pending-products" role="tabpanel" aria-labelledby="pending-tab">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm mới chờ duyệt</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table id="pending" class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 10%;">ID Sản phẩm</th>
                                            <th scope="col" style="width: 15%;">Hình ảnh</th>
                                            <th scope="col" style="width: 20%;">Tên sản phẩm</th>
                                            <th scope="col" style="width: 10%;">Mã SKU</th>
                                            <th scope="col" style="width: 15%;">Giá Sản phẩm</th>
                                            <th scope="col" style="width: 15%;">Tên Shop</th>
                                            <th scope="col" style="width: 10%;">Trạng thái</th>
                                            <th scope="col" style="width: 10%;">Ngày tạo</th>
                                            <th scope="col" style="width: 10%;">Hành động</th>
                                            

                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($pendingProducts->isEmpty())
                                            
                                        @else
                                            @foreach($pendingProducts as $product)
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
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 50px;" >{{$product->shop->shop_name}}</td> 
                                                    <td>
                                                        @if($product->status == 3)
                                                        Chưa duyệt
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <div>
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
                                                    </div>
                                                    <div class="mt-2">
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
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $product->id }}">
                                                            <button type="button" class="btn btn-primary" title="Chi tiết sản phẩm">
                                                                <i class="ri-eye-line align-middle"></i>
                                                            </button>
                                                        </a>
                                                    
                                                        <!-- Modal Chi tiết sản phẩm -->
                                                        <div class="modal fade" id="detailsModal-{{ $product->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $product->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="detailsModalLabel-{{ $product->id }}">Thông tin sản phẩm chi tiết</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="card shadow-sm">
                                                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                                <h5 class="mb-0 text-white">Sản phẩm chi tiết</h5>
                                                                                <span class="badge bg-success">
                                                                                    @if($product->status == 3)
                                                                                    Chưa duyệt
                                                                                        @endif
                                                                                </span>
                                                                            </div>
                                                                            <div class="card-body">
                                                                              <div class="mb-5 d-flex">
                                                                                    <span class="text-muted"> 
                                                                                       @foreach ($product->images as $image)
                                                                                           <img style="width:100px; height: 100px; " src="{{ $image->url }}" alt="Product Image">
                                                                                       @endforeach
                                                                                   </span>
                                                                                  
                                                                               </div>
                                                                                <div class="row">
                                                                                   
                                                                                    <!-- Cột trái -->
                                                                                    <div class="col-lg-6">
                                                                                      
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>ID:</strong> <span class="text-muted me-5">{{ $product->id }}</span> 
                                                                                        </div>
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>Tên sản phẩm: </strong> <span class="text-muted">{{ $product->name}}</span>
                                                                                        </div>
                                                                                       
                                                                                       
                                                                                        <div class="mb-3">
                                                                                            <strong>Mã SKU:</strong> <span class="text-muted">{{ $product->sku }}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Giá sản phẩm:</strong> <span class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Cột phải -->
                                                                                    <div class="col-lg-6">
                                                                                        <div class="mb-3">
                                                                                            <strong>Tên Shop</strong> <span class="text-muted">{{$product->shop->shop_name}}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Trạng thái:</strong> <span class="text-muted">
                                                                                                @if($product->status == 3)
                                                                                                Chưa duyệt
                                                                                                    @endif
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Ngày tạo:</strong> <span class="text-muted">{{ $product->created_at}}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Danh Sách Địa Chỉ -->
                                                                        <div class="mt-5">
                                                                            <h4 class="mb-3">Biến thể sản phẩm</h4>
                                                                            <table class="table table-bordered table-hover shadow-sm">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>id</th>
                                                                                        <th>Tên biến thể</th>
                                                                                        <th>Hình ảnh</th>
                                                                                        <th>Mã Sku</th>
                                                                                        <th>Giá </th>
                
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($product->variants as $variant)
                                                                                    <tr>
                                                                                        <td>{{ $variant->id }}</td>
                                                                                        <td>{{ $variant->name }}</td>
                                                                                        <td>
                                                                                           
                                                                                                <img src="{{ $variant->images }}" alt="Product Image" style="width: 50px; height: 50px; margin-right: 5px;">
                                                                        
                                                                                        </td>
                                                                                        <td>{{ $variant->sku }}</td>
                                                                                        <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                    
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        </div>
                                                                  
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                    </div>
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
    
                        <div class="mt-3">
                            <script>
                                new DataTable('#pending', {
                                    language: {   
                                        lengthMenu: "Hiển thị _MENU_ sản phẩm",
                                        search: "Tìm kiếm:" 
                                    }
                                });
                            </script>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <div class="tab-pane fade {{ $tab == 6 ? 'show active' : '' }}" id="pending-update-products" role="tabpanel" aria-labelledby="pending-update-tab">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm cập nhật chờ duyệt</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table id="update2" class="table align-middle table-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Sản phẩm</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Tên sản phẩm</th>
                                            <th scope="col">Mã SKU</th>
                                            <th scope="col">Giá Sản phẩm</th>
                                            <th scope="col">Tên Shop</th> 
                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($allUpdateProducts->isEmpty())
                                            
                                        @else
                                            @foreach($allUpdateProducts as $product)
                                                <tr>
                                                    <th scope="row"><a href="" class="fw-medium">{{ $product->product_id }}</a></th>
                                                    <td>
                                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" style="width: 50px; height: 50px;">
                                                        
                                                     
                                                    </td>
                                                    <td style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                    <td>{{$product->shop->shop_name ?? "Vô danh"}}</td> 
                                                    
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'handleUpdateProduct' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->product_id,
                                                                                                'action'=> 1,
                                                                                                'tab'=>6,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                        <!-- Không duyệt -->
                                                        <form action="{{ route('handleUpdateProduct',[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->product_id,
                                                                                                'action'=> 2,
                                                                                                'tab'=>6,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning" title="Không duyệt">
                                                                <i class="ri-close-circle-line align-middle"></i> 
                                                            </button>
                                                        </form>
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $product->id }}">
                                                            <button type="button" class="btn btn-primary" title="Chi tiết sản phẩm">
                                                                <i class="ri-eye-line align-middle"></i>
                                                            </button>
                                                        </a>
                                                    
                                                        <!-- Modal Chi tiết sản phẩm -->
                                                        <div class="modal fade" id="detailsModal-{{ $product->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $product->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="detailsModalLabel-{{ $product->id }}">Thông tin sản phẩm chi tiết</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="card shadow-sm">
                                                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                                <h5 class="mb-0 text-white">Sản phẩm chi tiết</h5>
                                                                                <span class="badge bg-success">
                                                                                    @if($product->status == 3)
                                                                                    Chưa duyệt
                                                                                        @endif
                                                                                </span>
                                                                            </div>
                                                                            <div class="card-body">
                                                                              <div class="mb-5 d-flex">
                                                                                    <span class="text-muted"> 
                                                                                       @if(is_array($product->images))
                                                                                            @foreach ($product->images as $image)
                                                                                                <img style="width:100px; height: 100px; " src="{{ $image->url }}" alt="Product Image">
                                                                                            @endforeach
                                                                                       @endif
                                                                                   </span>
                                                                                  
                                                                               </div>
                                                                                <div class="row">
                                                                                   
                                                                                    <!-- Cột trái -->
                                                                                    <div class="col-lg-6">
                                                                                      
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>ID:</strong> <span class="text-muted me-5">{{ $product->product_id }}</span> 
                                                                                        </div>
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>Tên sản phẩm: </strong> <span class="text-muted">{{ $product->name}}</span>
                                                                                        </div>
                                                                                       
                                                                                       
                                                                                        <div class="mb-3">
                                                                                            <strong>Mã SKU:</strong> <span class="text-muted">{{ $product->sku }}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Giá sản phẩm:</strong> <span class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Cột phải -->
                                                                                    <div class="col-lg-6">
                                                                                        <div class="mb-3">
                                                                                            <strong>Tên Shop</strong> <span class="text-muted">{{$product->shop->shop_name ?? "Vô danh"}}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                           
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Ngày tạo:</strong> <span class="text-muted">{{ $product->created_at}}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Danh Sách Địa Chỉ -->
                                                                        <div class="mt-5">
                                                                            <h4 class="mb-3">Biến thể sản phẩm</h4>
                                                                            <table class="table table-bordered table-hover shadow-sm">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>id</th>
                                                                                        <th>Tên biến thể</th>
                                                                                        <th>Hình ảnh</th>
                                                                                        <th>Mã Sku</th>
                                                                                        <th>số lượng tồn kho</th>
                                                                                        <th>Giá </th>
                
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @if(is_array(json_decode($product->change_of)))
                                                                                        @foreach (json_decode($product->change_of) as $variant)
                                                                                        <tr>
                                                                                            <td>{{ $variant->id }}</td>
                                                                                            <td>{{ $variant->name ?? "chưa nhập" }}</td>
                                                                                            <td>
                                                                                            
                                                                                                    <img src="{{ $variant->images }}" alt="Product Image" style="width: 50px; height: 50px; margin-right: 5px;">
                                                                            
                                                                                            </td>
                                                                                            <td>{{ $variant->sku }}</td>
                                                                                            <td>{{ $variant->stock }}</td>
                                                                                            <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                                                        </tr>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        </div>
                                                                  
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                        <div class="mt-3">
                            <script>
                                new DataTable('#update2', {
                                    language: {   
                                        lengthMenu: "Hiển thị _MENU_ sản phẩm",
                                        search: "Tìm kiếm:" 
                                    }
                                });
                            </script>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
      
        <div class="tab-pane fade {{ $tab == 3 ? 'show active' : '' }}" id="active-products" role="tabpanel" aria-labelledby="active-tab">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm đang hoạt động</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table id="active" class="table align-middle table-nowrap mb-0">
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
                                                    <!-- Button to open report modal -->
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $product->id }}">
                                                        <button type="button" class="btn btn-danger" title="Báo cáo vi phạm">
                                                            <i class="ri-error-warning-line align-middle"></i> 
                                                        </button>
                                                    </a>
                                                
                                                    <!-- Modal Báo cáo vi phạm -->
                                                    <div class="modal fade" id="reportModal-{{ $product->id }}" tabindex="-1" aria-labelledby="reportModalLabel-{{ $product->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="reportModalLabel-{{ $product->id }}">Báo cáo vi phạm</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('products.submitReport', ['id' => $product->id, 'token' => auth()->user()->refresh_token, 'tab' => 3]) }}" method="POST">
                                                                        @csrf
                                                                        <div class="mb-3">
                                                                            <label for="reason-{{ $product->id }}" class="form-label">Lý do vi phạm:</label>
                                                                            <textarea name="reason" id="reason-{{ $product->id }}" class="form-control" required></textarea>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $product->id }}">
                                                        <button type="button" class="btn btn-primary" title="Chi tiết sản phẩm">
                                                            <i class="ri-eye-line align-middle"></i>
                                                        </button>
                                                    </a>
                                                
                                                    <!-- Modal Chi tiết sản phẩm -->
                                                    <div class="modal fade" id="detailsModal-{{ $product->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $product->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="detailsModalLabel-{{ $product->id }}">Thông tin sản phẩm chi tiết</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="card shadow-sm">
                                                                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                            <h5 class="mb-0 text-white">Sản phẩm chi tiết</h5>
                                                                            <span class="badge bg-success">
                                                                                @if($product->status == 2)
                                                                                Hoạt động
                                                                                    @endif
                                                                            </span>
                                                                        </div>
                                                                        <div class="card-body">
                                                                          <div class="mb-5 d-flex">
                                                                                <span class="text-muted"> 
                                                                                   @foreach ($product->images as $image)
                                                                                       <img style="width:100px; height: 100px; " src="{{ $image->url }}" alt="Product Image">
                                                                                   @endforeach
                                                                               </span>
                                                                              
                                                                           </div>
                                                                            <div class="row">
                                                                               
                                                                                <!-- Cột trái -->
                                                                                <div class="col-lg-6">
                                                                                  
                                                                                    <div class="mb-3 d-flex ">
                                                                                        <strong>ID:</strong> <span class="text-muted me-5">{{ $product->id }}</span> 
                                                                                    </div>
                                                                                    <div class="mb-3 d-flex ">
                                                                                        <strong>Tên sản phẩm: </strong> <span class="text-muted">{{ $product->name}}</span>
                                                                                    </div>
                                                                                   
                                                                                   
                                                                                    <div class="mb-3">
                                                                                        <strong>Mã SKU:</strong> <span class="text-muted">{{ $product->sku }}</span>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <strong>Giá sản phẩm:</strong> <span class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- Cột phải -->
                                                                                <div class="col-lg-6">
                                                                                    <div class="mb-3">
                                                                                        <strong>Tên Shop</strong> <span class="text-muted">{{$product->shop->shop_name}}</span>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <strong>Trạng thái:</strong> <span class="text-muted">
                                                                                            @if($product->status == 2)
                                                                                           Hoạt động
                                                                                                @endif
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <strong>Ngày tạo:</strong> <span class="text-muted">{{ $product->created_at}}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Danh Sách Địa Chỉ -->
                                                                    <div class="mt-5">
                                                                        <h4 class="mb-3">Biến thể sản phẩm</h4>
                                                                        <table class="table table-bordered table-hover shadow-sm">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>id</th>
                                                                                    <th>Tên biến thể</th>
                                                                                    <th>Hình ảnh</th>
                                                                                    <th>Mã Sku</th>
                                                                                    <th>Giá </th>
            
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($product->variants as $variant)
                                                                                <tr>
                                                                                    <td>{{ $variant->id }}</td>
                                                                                    <td>{{ $variant->name }}</td>
                                                                                    <td>
                                                                                       
                                                                                            <img src="{{ $variant->images }}" alt="Product Image" style="width: 50px; height: 50px; margin-right: 5px;">
                                                                    
                                                                                    </td>
                                                                                    <td>{{ $variant->sku }}</td>
                                                                                    <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                                                </tr>
                                                                                @endforeach
                                                                                
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    </div>
                                                              
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                        <div class="mt-3">
                            <script>
                                new DataTable('#active', {
                                    language: {   
                                        lengthMenu: "Hiển thị _MENU_ sản phẩm",
                                        search: "Tìm kiếm:" 
                                    }
                                });
                            </script>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        
        <div class="tab-pane fade {{ $tab == 4 ? 'show active' : '' }}" id="rejected-products" role="tabpanel" aria-labelledby="rejected-tab">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm từ chối</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table id="rejected" class="table align-middle table-nowrap mb-0">
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
                                                        @if($product->status == 0)
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
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $product->id }}">
                                                            <button type="button" class="btn btn-primary" title="Chi tiết sản phẩm">
                                                                <i class="ri-eye-line align-middle"></i>
                                                            </button>
                                                        </a>
                                                    
                                                        <!-- Modal Chi tiết sản phẩm -->
                                                        <div class="modal fade" id="detailsModal-{{ $product->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $product->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="detailsModalLabel-{{ $product->id }}">Thông tin sản phẩm chi tiết</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="card shadow-sm">
                                                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                                <h5 class="mb-0 text-white">Sản phẩm chi tiết</h5>
                                                                                <span class="badge bg-success">
                                                                                    @if($product->status == 0)
                                                                                  Từ chối duyệt
                                                                                        @endif
                                                                                </span>
                                                                            </div>
                                                                            <div class="card-body">
                                                                              <div class="mb-5 d-flex">
                                                                                    <span class="text-muted"> 
                                                                                       @foreach ($product->images as $image)
                                                                                           <img style="width:100px; height: 100px; " src="{{ $image->url }}" alt="Product Image">
                                                                                       @endforeach
                                                                                   </span>
                                                                                  
                                                                               </div>
                                                                                <div class="row">
                                                                                   
                                                                                    <!-- Cột trái -->
                                                                                    <div class="col-lg-6">
                                                                                      
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>ID:</strong> <span class="text-muted me-5">{{ $product->id }}</span> 
                                                                                        </div>
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>Tên sản phẩm: </strong> <span class="text-muted">{{ $product->name}}</span>
                                                                                        </div>
                                                                                       
                                                                                       
                                                                                        <div class="mb-3">
                                                                                            <strong>Mã SKU:</strong> <span class="text-muted">{{ $product->sku }}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Giá sản phẩm:</strong> <span class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Cột phải -->
                                                                                    <div class="col-lg-6">
                                                                                        <div class="mb-3">
                                                                                            <strong>Tên Shop</strong> <span class="text-muted">{{$product->shop->shop_name}}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Trạng thái:</strong> <span class="text-muted">
                                                                                                @if($product->status == 0)
                                                                                                Từ chối duyệt
                                                                                                    @endif
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Ngày tạo:</strong> <span class="text-muted">{{ $product->created_at}}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-5">
                                                                            <h4 class="mb-3">Biến thể sản phẩm</h4>
                                                                            <table class="table table-bordered table-hover shadow-sm">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>id</th>
                                                                                        <th>Tên biến thể</th>
                                                                                        <th>Hình ảnh</th>
                                                                                        <th>Mã Sku</th>
                                                                                        <th>Giá </th>
                
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($product->variants as $variant)
                                                                                    <tr>
                                                                                        <td>{{ $variant->id }}</td>
                                                                                        <td>{{ $variant->name }}</td>
                                                                                        <td>
                                                                                           
                                                                                                <img src="{{ $variant->images }}" alt="Product Image" style="width: 50px; height: 50px; margin-right: 5px;">
                                                                        
                                                                                        </td>
                                                                                        <td>{{ $variant->sku }}</td>
                                                                                        <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                    
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        </div>
                                                                  
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                        <div class="mt-3">
                            
                            <script>
                                new DataTable('#rejected', {
                                    language: {   
                                        lengthMenu: "Hiển thị _MENU_ sản phẩm",
                                        search: "Tìm kiếm:" 
                                    }
                                });
                            </script>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
       
        <div class="tab-pane fade {{ $tab == 5 ? 'show active' : '' }}" id="violating-products" role="tabpanel" aria-labelledby="violating-tab">
            <div class="col-xl-12">
               
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm vi phạm</h4>
                    </div><!-- end card header -->
    
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="table-responsive">
                                <table id="report" class="table align-middle table-nowrap mb-0">
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
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $product->id }}">
                                                            <button type="button" class="btn btn-primary" title="Chi tiết sản phẩm">
                                                                <i class="ri-eye-line align-middle"></i>
                                                            </button>
                                                        </a>
                                                    
                                                        <div class="modal fade" id="detailsModal-{{ $product->id }}" tabindex="-1" aria-labelledby="detailsModalLabel-{{ $product->id }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="detailsModalLabel-{{ $product->id }}">Thông tin sản phẩm chi tiết</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="card shadow-sm">
                                                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                                <h5 class="mb-0 text-white">Sản phẩm chi tiết</h5>
                                                                                <span class="badge bg-success">
                                                                                    @if($product->status == 4)
                                                                                    Vi phạm
                                                                                        @endif
                                                                                </span>
                                                                            </div>
                                                                            <div class="card-body">
                                                                              <div class="mb-5 d-flex">
                                                                                    <span class="text-muted"> 
                                                                                       @foreach ($product->images as $image)
                                                                                           <img style="width:100px; height: 100px; " src="{{ $image->url }}" alt="Product Image">
                                                                                       @endforeach
                                                                                   </span>
                                                                                  
                                                                               </div>
                                                                                <div class="row">
                                                                                   
                                                                                    <!-- Cột trái -->
                                                                                    <div class="col-lg-6">
                                                                                      
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>ID:</strong> <span class="text-muted me-5">{{ $product->id }}</span> 
                                                                                        </div>
                                                                                        <div class="mb-3 d-flex ">
                                                                                            <strong>Tên sản phẩm: </strong> <span class="text-muted">{{ $product->name}}</span>
                                                                                        </div>
                                                                                       
                                                                                       
                                                                                        <div class="mb-3">
                                                                                            <strong>Mã SKU:</strong> <span class="text-muted">{{ $product->sku }}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Giá sản phẩm:</strong> <span class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Cột phải -->
                                                                                    <div class="col-lg-6">
                                                                                        <div class="mb-3">
                                                                                            <strong>Tên Shop</strong> <span class="text-muted">{{$product->shop->shop_name}}</span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Trạng thái:</strong> <span class="text-muted">
                                                                                                @if($product->status == 4)
                                                                                                Vi phạm
                                                                                                    @endif
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="mb-3">
                                                                                            <strong>Ngày tạo:</strong> <span class="text-muted">{{ $product->created_at}}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Danh Sách Địa Chỉ -->
                                                                        <div class="mt-5">
                                                                            <h4 class="mb-3">Biến thể sản phẩm</h4>
                                                                            <table class="table table-bordered table-hover shadow-sm">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>id</th>
                                                                                        <th>Tên biến thể</th>
                                                                                        <th>Hình ảnh</th>
                                                                                        <th>Mã Sku</th>
                                                                                        <th>Giá </th>
                
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($product->variants as $variant)
                                                                                    <tr>
                                                                                        <td>{{ $variant->id }}</td>
                                                                                        <td>{{ $variant->name }}</td>
                                                                                        <td>
                                                                                           
                                                                                                <img src="{{ $variant->images }}" alt="Product Image" style="width: 50px; height: 50px; margin-right: 5px;">
                                                                        
                                                                                        </td>
                                                                                        <td>{{ $variant->sku }}</td>
                                                                                        <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                    
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        </div>
                                                                  
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                        <div class="mt-3">
                            <script>
                                new DataTable('#report', {
                                    language: {   
                                        lengthMenu: "Hiển thị _MENU_ sản phẩm",
                                        search: "Tìm kiếm:" 
                                    }
                                });
                            </script>
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

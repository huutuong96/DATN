@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Tất cả</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Chờ duyệt</button>
          </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#hoat-dong" type="button" role="tab" aria-controls="hoat-dong" aria-selected="false">Đang hoạt động</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Đã từ chối duyệt</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link"  id="vi-pham-tab" data-bs-toggle="tab" data-bs-target="#vi-pham-tab-pane" type="button" role="tab" aria-controls="vi-pham-tab-pane" aria-selected="false">vi phạm</button>
          </li>
       
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0"> <div class="row">    
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
                                            <th scope="col">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($allProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
                                            </tr>
                                        @else
                                            @foreach($allProducts as $product)
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
                                                        Chưa duyệt
                                                    @elseif($product->status == 1)
                                                        Đang hoạt động
                                                    @elseif($product->status == 2)
                                                        Đã từ chối
                                                    @elseif($product->status == 3)
                                                        Vi phạm
                                                   
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        @if($product->status == 0)
                                                        <form action="{{ route('products.approve', [
                                                                                        'token' => auth()->user()->refesh_token,
                                                                                        'id' => $product->id,
                                                                                        ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                    
                                                        <form action="" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning" title="Không duyệt">
                                                                <i class="ri-close-circle-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($product->status == 1)

                                                        <form action="" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger" title="Báo cáo vi phạm">
                                                                <i class="ri-error-warning-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($product->status == 2 || $product->status == 3)
                                                        <form action="{{ route('products.approve', [
                                                                                        'token' => auth()->user()->refesh_token,
                                                                                        'id' => $product->id,
                                                                                        ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    
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
        </div></div>
        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0"> <div class="row">
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
                                                        @if($product->status == 0)
                                                        Chưa duyệt
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
                                                                                                ]) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" title="Duyệt">
                                                                <i class="ri-check-line align-middle"></i>
                                                            </button>
                                                        </form>
                                                     
                                                        <!-- Báo cáo vi phạm -->
                                                        {{-- <form action="{{ route('products.report', $product->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger" title="Báo cáo vi phạm">
                                                                <i class="ri-error-warning-line align-middle"></i> 
                                                            </button>
                                                        </form> --}}
                                                    
                                                        <!-- Không duyệt -->
                                                        <form action="{{ route('products.reject',[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
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
        </div></div>
        <div class="tab-pane fade" id="hoat-dong" role="tabpanel" aria-labelledby="profile-tab" tabindex="0"> <div class="row">
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
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
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
                                                        @if($product->status == 1)
                                                       Đang hoạt động
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                       
                                                     
                                                        <!-- Báo cáo vi phạm -->
                                                        <form action="" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger" title="Báo cáo vi phạm">
                                                                <i class="ri-error-warning-line align-middle"></i> 
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
        </div></div>
        <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0"> <div class="row">
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
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
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
                                                        @if($product->status == 2)
                                                        từ chối duyệt
                                                   
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
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
        </div></div>
        <div class="tab-pane fade" id="vi-pham-tab-pane" role="tabpanel" aria-labelledby="vi-pham-tab" tabindex="0"> <div class="row">
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
                                        @if($violatingProducts->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Không có sản phẩm nào chờ duyệt.</td>
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
                                                        @if($product->status == 3)
                                                        sản phẩm vi phạm
                                                   
                                                    @endif
                                                    <td>{{ $product->created_at}}</td>
                                                    <td>
                                                        <!-- Duyệt -->
                                                        <form action="{{ route( 'products.approve' ,[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $product->id,
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
        </div></div>

        

      </div>
   
   </div>
@endsection

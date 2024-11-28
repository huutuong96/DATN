@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách danh mục đã xóa</h4> 
                    <a
                        href="{{ route('list_category', ['token' => auth()->user()->refesh_token]) }}"
                        class="nav-link text-primary"
                        style="font-weight: bold;"
                        data-key="t-ecommerce"
                    >
                        list danh mục
                    </a>      
                </div><!-- end card header -->
                
                <div class="card-body">
                    <div class="live-preview">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên danh mục</th>
                                        <th scope="col">Hình ảnh</th>
                                        <th scope="col">Danh mục con của</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                
                                    @foreach($categories as $category)
                                    <tr>
                                        <th scope="row"><a href="#" class="fw-medium">{{$category->id}}</a></th>
                                        <td>{{$category->title}}</td>
                                        <td><img src="{{$category->image ?? 'assets/images/users/avatar-1.jpg'}}" alt="" class="avatar-xs rounded-circle me-2 material-shadow"></td>
                                        <td>{{$category->parent_id ?? "Đây là danh mục cha"}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                <a 
                                                    href="{{ route('change_category', [
                                                                                        'token' => auth()->user()->refesh_token,
                                                                                        'id' => $category->id,
                                                                                        'status' => 2,
                                                                                        ]) }}"
                                                >
                                                <button type="button" class="btn btn-info"
                                                title="Khôi phục"
                                                onclick="return confirm('Bạn có chắc chắn muốn khôi phục doanh mục này?');">
                                                <i class="ri-refresh-line align-middle"></i>
                                            </button>
                                                </a>
                                                </li>
                                                   
                                            </ul>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                            <div class="div mt-2">
                                {{ $categories->appends(['token' => auth()->user()->refesh_token])->links() }}
                            </div>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
   </div>


@endsection

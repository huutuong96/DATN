@extends('index')
@section('title', 'List Store')

@section('link')
<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
@endsection
@section('main')
   <div class="container-fluid">
    <div class="row">
        <nav>   
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button class="nav-link {{ $tab == 1 ? 'active' : '' }}" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="{{ $tab == 1 ? 'true' : 'false' }}">Tất cả</button>
              <button class="nav-link {{ $tab == 2  ? 'active' : '' }}" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="{{ $tab == 2 ? 'true' : 'false' }}">Đã xóa</button>
            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade {{ $tab == 1 ? 'show active' : '' }}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Tất cả bài viết</h4>
                             <!-- Toggle Between Modals -->
                             <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal">Thêm bài viết</button>
                             <!-- First modal dialog -->
                             <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                 <div class="modal-dialog modal-dialog-centered modal-xl">
                                     <div class="modal-content">
                                         <div class="modal-body text-center p-5">
                                            <form action="{{ route('posts.store', ['token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf <!-- Thêm CSRF token để bảo mật -->
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="title" class="form-label">Tiêu đề</label>
                                                            <input type="text" class="form-control" placeholder="Tiêu đề" id="title" name="title" required>
                                                        </div><!--end mb-3-->
                                                    </div><!--end col-->
                                            
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="blog_id" class="form-label">Chọn Blog</label>
                                                            <select class="form-control" id="blog_id" name="blog_id" required>
                                                                <option value="" disabled selected>Chọn blog</option>
                                                                @foreach($blogs as $blog)
                                                                    <option value="{{ $blog->id }}">{{ $blog->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div><!--end mb-3-->
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="image" class="form-label">Hình ảnh</label>
                                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                                        </div><!--end mb-3-->
                                                    </div><!--end col-->
                                            
                                            
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="content" class="form-label">Nội dung</label>
                                                            <textarea class="form-control" placeholder="Enter post content" id="summernote" name="content" rows="4" required></textarea>
                                                        </div><!--end mb-3-->
                                                    </div><!--end col-->
                                            
                                                   
                                                    <div class="col-lg-12">
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary">Thêm</button>
                                                        </div><!--end text-end-->
                                                    </div><!--end col-->
                                                </div><!--end row-->
                                            </form>
                                             
                                            <script>
                                                $(document).ready(function() {
                                                    $('#summernote').summernote({
                                                    placeholder: 'Nội dung bài viết',
                                                    tabsize: 2,
                                                    height: 100
                                                    });
                                                });
                                            </script>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </div><!-- end card header -->
                        
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive">
                                    <table id="postalll" class="table align-middle table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">blog</th>
                                                <th scope="col">tiêu đề</th>
                                                <th scope="col">Hình ảnh</th>
                                                <th scope="col">Người tạo</th>
                                                <th scope="col">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        
                                            @foreach($Posts as $Post)
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-medium">{{$Post->id}}</a></th>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->blog->name ?? "Danh mục đã bị xóa" }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->title }}</td>
                                                <td style="max-width: 100px; height: 90px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                    <img src="{{ $Post->image }}" alt="Post Image" style="max-width: 70%; height: auto;">
                                                </td>
                                                

                                                
                                                
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->user->fullname }}</td>
                                                <td>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $Post->id }}">
                                                        <button type="button" class="btn btn-primary" title="Chỉnh sửa">
                                                            <i class="ri-edit-line align-middle"></i>
                                                        </button>
                                                    </a>
                                                
                                                    <!-- Modal Chỉnh sửa -->
                                                    <div class="modal fade" id="editModal-{{ $Post->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $Post->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel-{{ $Post->id }}">Chỉnh sửa Post</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('post.update', ['id' => $Post->id, 'tab' => 1, 'token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="mb-3">
                                                                            <label for="blog_id-{{ $Post->id }}" class="form-label">Chọn Blog</label>
                                                                            <select name="blog_id" id="blog_id-{{ $Post->id }}" class="form-select" required>
                                                                                <option value="">-- Chọn Blog --</option>
                                                                                @foreach($blogs as $blog)
                                                                                    <option value="{{ $blog->id }}" {{ $Post->blog_id == $blog->id ? 'selected' : '' }}>
                                                                                        {{ $blog->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="title-{{ $Post->id }}" class="form-label">Tiêu đề</label>
                                                                            <input type="text" name="title" id="title-{{ $Post->id }}" class="form-control" value="{{ $Post->title }}" required>
                                                                        </div>                                             
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Ảnh hiện tại:</label>
                                                                            <div>
                                                                                <img  src="{{ $Post->image }}" alt="Ảnh hiện tại" style="max-width: 100px; height: 100px;">
                                                                            </div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="image-{{ $Post->id }}" class="form-label">Cập nhật ảnh mới (tùy chọn)</label>
                                                                            <input type="file" name="image" id="image-{{ $Post->id }}" class="form-control" accept="image/*">
                                                                        </div>
                                                
                                                                        <div class="mb-3">
                                                                            <label for="content-{{ $Post->id }}" class="form-label">Nội dung:</label>
                                                                            <textarea name="content" id="summernote{{ $Post->id }}" class="form-control" rows="4" required>{{ $Post->content }}</textarea>
                                                                        </div>
                                                
                                                
                                                                        <input type="hidden" name="update_by" value="{{ auth()->user()->id }}">
                                                                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                                    </form>
                                                                    <script>
                                                                        $(document).ready(function() {
                                                                            $('#summernote{{ $Post->id }}').summernote({
                                                                                placeholder: 'Nội dung bài viết',
                                                                                tabsize: 2,
                                                                                height: 100
                                                                            });
                                                                        });
                                                                    </script>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                
                                                    <!-- Delete form -->
                                                    <form action="{{ route('post.destroy', ['token' => auth()->user()->refesh_token, 'id' => $Post->id, 'tab' => 1]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')                                                       
                                                            <button type="submit" class="btn btn-danger" title="Xóa"  onclick="return confirm('Bạn có chắc chắn muốn xóa post này?');">
                                                                <i class="ri-delete-bin-line align-middle"></i>
                                                        </button>
                                                    </form>
                                                    <!-- Button to open blog details modal -->
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#blogDetailsModal-{{ $Post->id }}">
                                                        <button type="button" class="btn btn-primary" title="Chi tiết bài viết">
                                                            <i class="ri-eye-line align-middle"></i> 
                                                        </button>
                                                    </a>

                                                    <!-- Modal Chi tiết bài viết -->
                                                    <div class="modal fade" id="blogDetailsModal-{{ $Post->id }}" tabindex="-1" aria-labelledby="blogDetailsModalLabel-{{ $Post->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="blogDetailsModalLabel-{{ $Post->id }}">Chi tiết Bài viết</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body ">
                                                                    {!! $Post->content !!}
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#summernote2{{ $Post->id }}').summernote({
                                                                placeholder: 'Nội dung bài viết',
                                                                tabsize: 2,
                                                                height: 300,
                                                                width:950,
                                                                lineHeights: ['0.8', '1.2', '1.5', '1.8', '2.0', '2.5'] // Dòng tự động xuống hàng
                                                            });
                                                        });
                                                    </script>
                                                    
                                                </td>
                                                
                                                
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                   
        
                                </div>
                            </div>
                            <div class="mt-3">
                                <script>
                                    new DataTable('#postalll', {
                                        language: {   
                                            lengthMenu: "Hiển thị _MENU_ Doanh mục bài viết",
                                            search: "Tìm kiếm:"
                                        },
                                       
                                    });
                                </script>
                                
                                </div>
                        </div><!-- end card-body -->
                       
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <div class="tab-pane fade {{ $tab == 2 ? 'show active' : '' }}" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Tất cả bài viết</h4>
                        </div><!-- end card header -->
                        
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive">
                                    <table id="postallldelete" class="table align-middle table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">blog</th>
                                                <th scope="col">Hình ảnh</th>
                                                <th scope="col">tiêu đề</th>
                                                <th scope="col">nội dung</th>
                                                <th scope="col">Người tạo</th>
                                                <th scope="col">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        
                                            @foreach($deletedPost as $Post)
                                            <tr>
                                                {{-- @dd( $Post->posts) --}}
                                                <th scope="row"><a href="#" class="fw-medium">{{$Post->id}}</a></th>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->blog_id}}</td>
                                                <td style="max-width: 100px; height: 90px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                    <img src="{{ $Post->image }}" alt="Post Image" style="max-width: 70%; height: auto;">
                                                </td>                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->title }}</td>
                                                <td style="max-width: 200px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ \Illuminate\Support\Str::limit($Post->content, 150, '...') }}
                                                </td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $Post->user->fullname }}</td>
                                                <td>
                                                    <form action="{{ route('post.restore',[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $Post->id,
                                                                                                'tab'=>2,
                                                                                                ]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-info"
                                                                    title="Khôi phục"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn khôi phục bài viết này?');">
                                                                    <i class="ri-refresh-line align-middle"></i>
                                                                </button>
                                                    </form>
                                                </td>
                                                
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                   
        
                                </div>
                            </div>
                            <div class="mt-3">
                                <script>
                                    new DataTable('#postallldelete', {
                                        language: {   
                                            lengthMenu: "Hiển thị _MENU_ Doanh mục bài viết",
                                            search: "Tìm kiếm:"
                                        },
                                       
                                    });
                                </script>
                                </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
          </div>
      
    </div>
   </div>


@endsection

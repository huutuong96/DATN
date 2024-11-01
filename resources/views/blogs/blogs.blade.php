@extends('index')
@section('title', 'List Store')

@section('main')
   <div class="container-fluid">
    <div class="row">
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
                            <h4 class="card-title mb-0 flex-grow-1">Danh mục bài viết</h4>
                        </div><!-- end card header -->
                        
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Tên blog</th>
                                                <th scope="col">slug</th>
                                                <th scope="col">tiêu đề</th>
                                                <th scope="col">Người tạo</th>
                                                <th scope="col">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        
                                            @foreach($blogs as $blog)
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-medium">{{$blog->id}}</a></th>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->name }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->slug }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->title }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->user->fullname }}</td>
                                                <td>
                                                                                                     
                                                        <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $blog->id }}">
                                                            <button type="button" class="btn btn-primary" title="Chỉnh sửa">
                                                                Chỉnh sửa
                                                            </button>
                                                        </a>

                                                        <!-- Modal Chỉnh sửa -->
                                                        <div class="modal fade" id="editModal-{{ $blog->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $blog->id }}" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editModalLabel-{{ $blog->id }}">Chỉnh sửa Blog</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="{{ route('blogs.update', [
                                                                            'id' => $blog->id,
                                                                            'tab'=>1,
                                                                            'token' => auth()->user()->refesh_token,
                                                                        ]) }}" method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <!-- Tiêu đề -->
                                                                            <div class="mb-3">
                                                                                <label for="title-{{ $blog->id }}" class="form-label">Tên Blog</label>
                                                                                <input type="text" name="name" id="title-{{ $blog->id }}" class="form-control" value="{{ $blog->name }}" required>
                                                                            </div>
                                                                            <!-- Nội dung -->
                                                                            <div class="mb-3">
                                                                                <label for="content-{{ $blog->id }}" class="form-label">Nội dung:</label>
                                                                                <textarea name="title" id="content-{{ $blog->id }}" class="form-control" rows="4" required>{{ $blog->title }}</textarea>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <form action="{{ route('blogs.destroy',[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $blog->id,
                                                                                                'tab'=>1,
                                                                                                ]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" style="background: ;border-radius: 5px; border: 1px solid black; color: red; cursor: pointer; height: 37px; width: 80px;" onclick="return confirm('Bạn có chắc chắn muốn xóa blog này?');">
                                                            🗑️ Xóa
                                                        </button>
                                                        
                                                    </form>

                                                </td>
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                   
        
                                </div>
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
                            <h4 class="card-title mb-0 flex-grow-1">Danh mục bài viết</h4>
                            
                        </div><!-- end card header -->
                        
                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Tên blog</th>
                                                <th scope="col">slug</th>
                                                <th scope="col">tiêu đề</th>
                                                <th scope="col">Người tạo</th>
                                                <th scope="col">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        
                                            @foreach($deletedBlog as $blog)
                                            <tr>
                                                <th scope="row"><a href="#" class="fw-medium">{{$blog->id}}</a></th>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->name }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->slug }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->title }}</td>
                                                <td style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">{{ $blog->user->fullname }}</td>
                                                <td>
                                                    <form action="{{ route('blogs.restore',[
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $blog->id,
                                                                                                'tab'=>2,
                                                                                                ]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" style="background: none; border: none; color: green; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn khôi phục blog này?');">
                                                            🔄 Khôi Phục
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            {{-- {{ $blogs->appends(['token' => auth()->user()->token])->links() }} --}}
                                        
        
                                </div>
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

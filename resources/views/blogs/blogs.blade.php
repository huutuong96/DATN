@extends('index')
@section('title', 'List Store')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
@section('main')
    <div class="container-fluid">
        <div class="row">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link {{ $tab == 1 ? 'active' : '' }}" id="nav-home-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                        aria-selected="{{ $tab == 1 ? 'true' : 'false' }}">Tất cả</button>
                    <button class="nav-link {{ $tab == 2 ? 'active' : '' }}" id="nav-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="{{ $tab == 2 ? 'true' : 'false' }}">Đã xóa</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade {{ $tab == 1 ? 'show active' : '' }}" id="nav-home" role="tabpanel"
                    aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Danh mục bài viết</h4>

                                <!-- Toggle Between Modals -->
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#firstmodal">Thêm danh mục bài viết</button>
                                <!-- First modal dialog -->
                                <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..."
                                    tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <form id="addBlogForm"
                                                    action="{{ route('blogs.store', [
                                                        'token' => auth()->user()->refesh_token,
                                                    ]) }}"
                                                    method="POST">
                                                    @csrf <!-- Thêm CSRF token để bảo mật -->
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="name" class="form-label">Tên Doanh mục bài viết</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Tên Doanh mục bài viết" id="name"
                                                                    name="name" required>
                                                            </div><!--end mb-3-->
                                                        </div><!--end col-->

                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="title" class="form-label">Tiêu đề</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Tiêu đề" id="title"
                                                                    name="title" required>
                                                            </div><!--end mb-3-->
                                                        </div><!--end col-->

                                                        <div class="col-lg-12">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-primary">Thêm</button>
                                                            </div><!--end text-end-->
                                                        </div><!--end col-->
                                                    </div><!--end row-->
                                                </form>



                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card header -->
                            <div class="card-body">
                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table id="blogallnew" class="table align-middle table-nowrap mb-0">
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

                                                @foreach ($blogs as $blog)
                                                    <tr>
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $blog->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->name }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->slug }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->title }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->user->fullname }}</td>
                                                        <td>

                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editModal-{{ $blog->id }}">
                                                                <button type="button" class="btn btn-primary"
                                                                    title="Chỉnh sửa">
                                                                    <i class="ri-edit-line align-middle"></i>
                                                                </button>
                                                            </a>

                                                            <!-- Modal Chỉnh sửa -->
                                                            <div class="modal fade" id="editModal-{{ $blog->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="editModalLabel-{{ $blog->id }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="editModalLabel-{{ $blog->id }}">
                                                                                Chỉnh sửa Blog</h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form
                                                                                action="{{ route('blogs.update', [
                                                                                    'id' => $blog->id,
                                                                                    'tab' => 1,
                                                                                    'token' => auth()->user()->refesh_token,
                                                                                ]) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <!-- Tiêu đề -->
                                                                                <div class="mb-3">
                                                                                    <label for="title-{{ $blog->id }}"
                                                                                        class="form-label">Tên Blog</label>
                                                                                    <input type="text" name="name"
                                                                                        id="title-{{ $blog->id }}"
                                                                                        class="form-control"
                                                                                        value="{{ $blog->name }}"
                                                                                        required>
                                                                                </div>
                                                                                <!-- Nội dung -->
                                                                                <div class="mb-3">
                                                                                    <label
                                                                                        for="content-{{ $blog->id }}"
                                                                                        class="form-label">Nội
                                                                                        dung:</label>
                                                                                    <textarea name="title" id="content-{{ $blog->id }}" class="form-control" rows="4" required>{{ $blog->title }}</textarea>
                                                                                </div>
                                                                                <button type="submit"
                                                                                    class="btn btn-primary">Lưu thay
                                                                                    đổi</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <form
                                                                action="{{ route('blogs.destroy', [
                                                                    'token' => auth()->user()->refesh_token,
                                                                    'id' => $blog->id,
                                                                    'tab' => 1,
                                                                ]) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger"
                                                                    title="Xóa"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa blog này?');">
                                                                    <i class="ri-delete-bin-line align-middle"></i>
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
                                        new DataTable('#blogallnew', {
                                            language: {   
                                                lengthMenu: "Hiển thị _MENU_ Doanh mục bài viết",
                                                search: "Tìm kiếm:"
                                            },
                                            initComplete: function () {
                                                document.querySelector('#blogallnew_wrapper').style.fontFamily = '"Times New Roman", Times, serif';
                                                document.querySelectorAll('#blogallnew thead th').forEach(th => {
                                                    th.style.fontFamily = '"Times New Roman", Times, serif';
                                                    th.style.fontWeight = 'bold'; 
                                                });
                                                document.querySelectorAll('#blogallnew tbody td').forEach(td => {
                                                    td.style.fontFamily = '"Times New Roman", Times, serif';
                                                });
                                                document.querySelector('#blogallnew_filter label').style.fontFamily = '"Times New Roman", Times, serif';
                                                document.querySelector('#blogallnew_length label').style.fontFamily = '"Times New Roman", Times, serif';
                                            }
                                        });
                                    </script>
                                    
                                    
                                    
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <div class="tab-pane fade {{ $tab == 2 ? 'show active' : '' }}" id="nav-profile" role="tabpanel"
                    aria-labelledby="nav-profile-tab" tabindex="0">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Danh mục bài viết</h4>

                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table id="blogalldelete" class="table align-middle table-nowrap mb-0">
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

                                                @foreach ($deletedBlog as $blog)
                                                    <tr>
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $blog->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->name }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->slug }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->title }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $blog->user->fullname }}</td>
                                                        <td>
                                                            <form
                                                                action="{{ route('blogs.restore', [
                                                                    'token' => auth()->user()->refesh_token,
                                                                    'id' => $blog->id,
                                                                    'tab' => 2,
                                                                ]) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-info"
                                                                    title="Khôi phục"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn khôi phục blog này?');">
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
                                        new DataTable('#blogalldelete', {
                                            language: {   
                                                lengthMenu: "Hiển thị _MENU_ Doanh mục bài viết",
                                                search: "Tìm kiếm:"
                                            },
                                            initComplete: function () {
                                                document.querySelector('#blogalldelete_wrapper').style.fontFamily = '"Times New Roman", Times, serif';
                                                document.querySelectorAll('#blogalldelete thead th').forEach(th => {
                                                    th.style.fontFamily = '"Times New Roman", Times, serif';
                                                    th.style.fontWeight = 'bold'; 
                                                });
                                                document.querySelectorAll('#blogalldelete tbody td').forEach(td => {
                                                    td.style.fontFamily = '"Times New Roman", Times, serif';
                                                });
                                                document.querySelector('#blogalldelete_filter label').style.fontFamily = '"Times New Roman", Times, serif';
                                                document.querySelector('#blogalldelete_length label').style.fontFamily = '"Times New Roman", Times, serif';
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

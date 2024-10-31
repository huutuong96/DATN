@extends('index')
@section('title', 'Danh sách phân quyền')

@section('main')

<div class="container-fluid">

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
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
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách phân quyền</h4>
                    <!-- Toggle Between Modals -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal">Thêm phân quyền</button>
                    <!-- First modal dialog -->
                    <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <form action="{{ route('role_store', ['token' => auth()->user()->refesh_token]) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Tên</label>
                                                    <input name="title" type="text" class="form-control" placeholder="Enter category title" id="title" required>
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="index" class="form-label">Mô tả</label>
                                                    <input name="description" type="number" class="form-control" value="1" id="index" required>
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
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
                            <table class="table align-middle table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên phân quyền</th>
                                        <th scope="col">phân quyền con của</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($roles as $role)
                                    <tr>
                                        <th scope="row"><a href="#" class="fw-medium">{{$role->id}}</a></th>
                                        <td>{{$role->title}}</td>
                                        <td>{{$role->parent_id ?? "Đây là phân quyền cha"}}</td>
                                        <td>
                                            <ul class="list-inline">
                                                @if ($role->status == 1)
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('change_category', [
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $role->id,
                                                                                                'status' => 2,
                                                                                                ]) }}">
                                                        <button type="button" class="btn btn-warning waves-effect waves-light">Tắt</button>
                                                    </a>
                                                </li>
                                                @elseif ($role->status == 2)
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('change_category', [
                                                                                            'token' => auth()->user()->refesh_token,
                                                                                            'id' => $role->id,
                                                                                            'status' => 1,
                                                                                            ]) }}">
                                                        <button type="button" class="btn btn-success waves-effect waves-light">Bật</button>


                                                </li>
                                                @endif
                                                <li class="list-inline-item">
                                                    <!-- Toggle Between Modals -->
                                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal{{ $role->id }}">Chỉnh sửa</button>
                                                    <!-- First modal dialog -->
                                                    <div class="modal fade" id="firstmodal{{ $role->id }}" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-body text-center p-5">
                                                                    <form id="updateCategoryForm" action="" method="">
                                                                        <input type="hidden" name="id" value="{{$role->id}}"> <!-- Thêm trường để gửi ID của phân quyền -->
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="titleInput" class="form-label">Title</label>
                                                                                    <input name="title" type="text" class="form-control" value="{{$role->title}}" id="titleInput" required>
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <input type="hidden" name="index" class="form-control" value="{{$role->index}}" id="indexInput" required>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="imageInput" class="form-label">Image URL</label>
                                                                                    <input type="file" name="image" class="form-control" placeholder="Enter image URL" id="imageInput">
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <input type="hidden" name="status" class="form-control" value="{{$role->status}}" id="statusInput" required>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="parentIdInput" class="form-label">Parent ID</label>
                                                                                    <input type="number" name="parent_id" class="form-control" value="{{$role->parent_id }}" id="parentIdInput">
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <div class="col-lg-12">
                                                                                <div class="text-end">
                                                                                    <button type="submit" onclick="updateCategory({{ $role->id }})" class="btn btn-primary">Submit</button>
                                                                                </div>
                                                                            </div><!--end col-->
                                                                        </div><!--end row-->
                                                                    </form>

                                                                    <script>
                                                                        document.getElementById('updateCategoryForm').addEventListener('submit', function(event) {
                                                                            event.preventDefault(); // Ngăn chặn hành vi mặc định của form

                                                                            // Lấy ID phân quyền từ một input hidden
                                                                            const categoryId = document.getElementById('categoryIdInput').value; // Giả sử có input hidden với ID này

                                                                            // Cập nhật phân quyền
                                                                            updateCategory(categoryId); // Gọi hàm cập nhật phân quyền
                                                                        });

                                                                        // Hàm lấy token từ URL
                                                                        function getTokenFromURL() {
                                                                            const urlParams = new URLSearchParams(window.location.search);
                                                                            return urlParams.get('token'); // Giả sử token nằm trong query string dưới dạng 'token=YOUR_TOKEN'
                                                                        }

                                                                        // Hàm cập nhật phân quyền
                                                                        async function updateCategory(categoryId) {
                                                                            const token = getTokenFromURL();

                                                                            const formData = {
                                                                                title: document.getElementById('titleInput').value,
                                                                                index: document.getElementById('indexInput').value,
                                                                                image: document.getElementById('imageInput').value,
                                                                                status: document.getElementById('statusInput').value,
                                                                                parent_id: document.getElementById('parentIdInput').value,
                                                                                update_by: {
                                                                                    {
                                                                                        auth() - > user() - > id
                                                                                    }
                                                                                } // ID người cập nhật
                                                                            };
                                                                            console.log(formData);
                                                                            try {
                                                                                const res = await fetch(`https://vnshop.top/api/categories/${categoryId}`, {
                                                                                    method: 'PUT',
                                                                                    headers: {
                                                                                        'Content-Type': 'application/json',
                                                                                        'Authorization': `Bearer ${token}` // Gắn Bearer Token vào header
                                                                                    },
                                                                                    body: JSON.stringify(formData)
                                                                                });
                                                                                const payload = await res.json();
                                                                                if (!res.ok) {
                                                                                    throw new Error('Network response was not ok');
                                                                                }
                                                                                alert('Cập nhật thành công!');
                                                                                window.location.reload();
                                                                            } catch (error) {
                                                                                console.error('Error:', error);
                                                                                alert('Đã xảy ra lỗi: ' + error.message);
                                                                            }

                                                                        }
                                                                    </script>




                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('role_destroy', ['token' => auth()->user()->refesh_token,
                                                                                            'id' => $role->id,
                                                                                            'status' => 0,
                                                                                            ]) }}">
                                                        <button type="button" class="btn btn-danger waves-effect waves-light">Xóa</button>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            <div class="div mt-2">
                                {{ $roles->appends(['token' => auth()->user()->refesh_token])->links() }}
                            </div>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">...</div>
        <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">...</div>
        <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">...</div>


    </div>
</div>


@endsection
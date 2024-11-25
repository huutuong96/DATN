@extends('index')
@section('title', 'List Store')

@section('main')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Danh sách danh mục</h4>
                    <!-- Toggle Between Modals -->
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal">Thêm danh mục</button>
                    <!-- First modal dialog -->
                    <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center p-5">
                                    <form id="addCategoryForm">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Tiêu đề</label>
                                                    <input type="text" class="form-control" placeholder="tiêu đề doanh mục" id="title" required>
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="index" class="form-label">vị trí</label>
                                                    <input type="number" class="form-control" value="1" id="index" required>
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Hình ảnh</label>
                                                    <input type="file" class="form-control" placeholder="Enter image URL" id="image">
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Trạng thái</label>
                                                    <input type="number" class="form-control" value="1" id="status" required>
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="parentId" class="form-label">Doanh mục cha</label>
                                                    <input type="number" class="form-control" placeholder="Enter parent ID" id="parentId">
                                                </div><!--end mb-3-->
                                            </div><!--end col-->
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="taxId" class="form-label">Thuế</label>
                                                    <input type="number"
                                                        class="form-control"
                                                        placeholder="Nhập số thuế"
                                                        id="taxId"
                                                        name="tax_id"
                                                        min="0"
                                                        required>
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
                                        document.getElementById('addCategoryForm').addEventListener('submit', async function(event) {
                                            event.preventDefault(); // Ngăn chặn hành vi mặc định của form
                                            await addCategory();
                                        });

                                        async function addCategory() {
                                            const urlParams = new URLSearchParams(window.location.search);
                                            const token = urlParams.get('token');
                                            console.log(token);

                                            const title = document.getElementById('title').value;
                                            const index = document.getElementById('index').value;
                                            const image = document.getElementById('image').files; // Nếu bạn có URL của ảnh
                                            const status = document.getElementById('status').value;
                                            const parentId = document.getElementById('parentId').value;
                                            const tax = document.getElementById('tax').value;
                                            console.log(image);

                                            const data = {
                                                title: title,
                                                index: index,
                                                image: image,
                                                status: status,
                                                parent_id: parentId,
                                                update_by: {
                                                    {
                                                        auth() - > user() - > id
                                                    }
                                                }
                                            };

                                            try {
                                                const res = await fetch(`https://vnshop.top/api/categories`, {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'Authorization': `Bearer ${token}` // Gắn Bearer Token vào header
                                                    },
                                                    body: JSON.stringify(data)
                                                });
                                                const payload = await res.json();
                                                if (!res.ok) {
                                                    throw new Error('Network response was not ok');
                                                }
                                                alert('Thêm danh mục thành công!');
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
                                        <th scope="col">Thuế</th>
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
                                            <div class="col-lg-8">
                                                <select name="tax_id" class="form-control js-example-templating">
                                                    <option>Danh Mục Chưa Có Thuế</option>
                                                    @foreach($taxes as $tax)
                                                    @php
                                                    $selected = false;
                                                    @endphp
                                                    @foreach($tax_category as $tc)
                                                    @if($tc->category_id == $category->id && $tc->tax_id == $tax->id)
                                                    @php
                                                    $selected = true;
                                                    @endphp
                                                    @endif
                                                    @endforeach
                                                    <option value="{{$tax->id}}" {{ $selected ? 'selected' : '' }}>{{$tax->title}} | {{$tax->rate}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="list-inline">
                                                @if ($category->status == 1)
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('change_category', [
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $category->id,
                                                                                                'status' => 2,
                                                                                                ]) }}">
                                                        <button type="button" class="btn btn-warning waves-effect waves-light" title="Khóa">
                                                            <i class="ri-lock-line align-middle"></i>
                                                        </button>
                                                    </a>
                                                </li>
                                                @elseif ($category->status == 2)
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('change_category', [
                                                                                            'token' => auth()->user()->refesh_token,
                                                                                            'id' => $category->id,
                                                                                            'status' => 1,
                                                                                            ]) }}">
                                                        <button type="button" class="btn btn-success" title="Bật">
                                                            <i class="ri-check-line align-middle"></i>
                                                        </button>

                                                </li>
                                                @endif
                                                <li class="list-inline-item">
                                                    <!-- Toggle Between Modals -->
                                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#firstmodal{{ $category->id }}" title="Chỉnh sửa"><i class="ri-edit-line align-middle"></i></button>
                                                    <!-- First modal dialog -->
                                                    <div class="modal fade" id="firstmodal{{ $category->id }}" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-body text-center p-5">
                                                                    <form id="updateCategoryForm" action="" method="">
                                                                        <input type="hidden" name="id" value="{{$category->id}}"> <!-- Thêm trường để gửi ID của danh mục -->
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="titleInput" class="form-label">Tiêu đề</label>
                                                                                    <input name="title" type="text" class="form-control" value="{{$category->title}}" id="titleInput" required>
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <input type="hidden" name="index" class="form-control" value="{{$category->index}}" id="indexInput" required>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="imageInput" class="form-label">Hình ảnh</label>
                                                                                    <input type="file" name="imageInput" class="form-control" placeholder="Enter image URL" id="imageInput">
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <input type="hidden" name="status" class="form-control" value="{{$category->status}}" id="statusInput" required>
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="parentIdInput" class="form-label">Doanh mục cha</label>
                                                                                    <input type="number" name="parent_id" class="form-control" value="{{$category->parent_id }}" id="parentIdInput">
                                                                                </div>
                                                                            </div><!--end col-->
                                                                            <div class="col-lg-12 mb-3">
                                                                                <label for="tax_id" class="form-label">Chọn thuế danh mục</label>
                                                                                <select id="tax_id" name="tax_id" class="form-control js-example-templating">
                                                                                    <option>Chọn Thuế Danh Mục</option>
                                                                                    @foreach($taxes as $tax)
                                                                                    @php
                                                                                    $selected = false;
                                                                                    @endphp
                                                                                    @foreach($tax_category as $tc)
                                                                                    @if($tc->category_id == $category->id && $tc->tax_id == $tax->id)
                                                                                    @php
                                                                                    $selected = true;
                                                                                    @endphp
                                                                                    @endif
                                                                                    @endforeach
                                                                                    <option value="{{$tax->id}}" {{ $selected ? 'selected' : '' }}>{{$tax->title}} | {{$tax->rate}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-lg-12">
                                                                                <div class="text-end">
                                                                                    <button type="submit" onclick="updateCategory({{ $category->id }})" class="btn btn-primary">Submit</button>
                                                                                </div>
                                                                            </div><!--end col-->
                                                                        </div><!--end row-->
                                                                    </form>

                                                                    <script>
                                                                        document.getElementById('updateCategoryForm').addEventListener('submit', function(event) {
                                                                            event.preventDefault(); // Ngăn chặn hành vi mặc định của form

                                                                            // Lấy ID danh mục từ một input hidden
                                                                            const categoryId = document.getElementById('categoryIdInput').value; // Giả sử có input hidden với ID này

                                                                            // Cập nhật danh mục
                                                                            updateCategory(categoryId); // Gọi hàm cập nhật danh mục
                                                                        });

                                                                        // Hàm lấy token từ URL
                                                                        function getTokenFromURL() {
                                                                            const urlParams = new URLSearchParams(window.location.search);
                                                                            return urlParams.get('token'); // Giả sử token nằm trong query string dưới dạng 'token=YOUR_TOKEN'
                                                                        }

                                                                        // Hàm cập nhật danh mục
                                                                        async function updateCategory(categoryId) {
                                                                            const token = getTokenFromURL();

                                                                            const formData = {
                                                                                title: document.getElementById('titleInput').value,
                                                                                index: document.getElementById('indexInput').value,
                                                                                image: document.getElementById('imageInput').files[0],
                                                                                status: document.getElementById('statusInput').value,
                                                                                parent_id: document.getElementById('parentIdInput').value,
                                                                                tax_id: document.getElementById('tax_id').value,
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
                                                        href="{{ route('change_category', [
                                                                                            'token' => auth()->user()->refesh_token,
                                                                                            'id' => $category->id,
                                                                                            'status' => 5,
                                                                                            ]) }}">
                                                        <button type="button" class="btn btn-danger waves-effect waves-light" title="xóa"><i class="ri-delete-bin-line align-middle"></i></button>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    {{ $categories->appends(['token' => auth()->user()->refesh_token])->links() }}
                                </div>
                                <a
                                    href="{{ route('trash_category',['token' => auth()->user()->refesh_token]) }}"
                                    class="nav-link text-primary"
                                    style="font-weight: bold;"
                                    data-key="t-ecommerce">
                                    Danh mục đã xóa
                                </a>
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
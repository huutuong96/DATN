@extends('index')
@section('title', 'List Store')

@section('link')
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
@endsection
@section('main')
    <div class="container-fluid">
        <div class="row">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link {{ $tab == 1 ? 'active' : '' }}" id="nav-home-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                        aria-selected="{{ $tab == 1 ? 'true' : 'false' }}">Hoạt động</button>
                    <button class="nav-link {{ $tab == 2 ? 'active' : '' }}" id="nav-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="{{ $tab == 2 ? 'true' : 'false' }}">Đã Tắt </button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade {{ $tab == 1 ? 'show active' : '' }}" id="nav-home" role="tabpanel"
                    aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Tất cả Banner</h4>
                                <!-- Toggle Between Modals -->
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#firstmodal">Thêm Banner</button>
                                <!-- First modal dialog -->
                                <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..."
                                    tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <form action="{{ route('banner.store', ['token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="title" class="form-label">Tiêu đề</label>
                                                                <input type="text" class="form-control" id="title" name="title" placeholder="Tiêu đề" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="content" class="form-label">Nội dung</label>
                                                                <textarea class="form-control" id="content" name="content" rows="2" placeholder="Nội dung" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="image" class="form-label">Hình ảnh</label>
                                                                <input type="file" class="form-control" id="image" name="image" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="URL" class="form-label">Đường dẫn</label>
                                                                <input type="text" class="form-control" id="URL" name="URL" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Trạng thái</label>
                                                                <select style="width: 507px;left: 2rem;" style="width: 130px" class="form-control" id="status" name="status" required>
                                                                    <option value="" disabled selected>Chọn trạng thái</option>
                                                                    <option value="2">Active</option>
                                                                    <option value="3">Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="index" class="form-label">Thứ tự</label>
                                                                <input type="number" class="form-control" id="index" name="index" placeholder="Thứ tự hiển thị" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <th scope="col">tiêu đề</th>
                                                    <th scope="col">nội dung</th>
                                                    <th scope="col">Hình ảnh</th>
                                                    <th scope="col">Đường dẫn</th>
                                                    <th scope="col">Trạng thái</th>
                                                    <th scope="col">Vị trí</th>
                                                    <th scope="col">Người tạo</th>
                                                    <th scope="col">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($banners as $banner)
                                                    <tr>
                                                 
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $banner->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $banner->title }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ \Illuminate\Support\Str::limit($banner->content, 150, '...') }}
                                                        </td>
                                                        <td style="max-width: 100px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            <img src="{{ $banner->image }}" alt="Post Image" style="max-width: 50%; height: auto;">
                                                        </td>
                                                        <td style="max-width: 200px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ \Illuminate\Support\Str::limit($banner->URL, 150, '...') }}
                                                        </td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            @if($banner->status == 2)
                                                            Hoạt động
                                                        @elseif($banner->status == 3)
                                                            Không hoạt động
                                                        @endif
                                                        </td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $banner->index }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $banner->user->fullname }}</td>
                                                            <td>
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $banner->id }}">
                                                                    <button type="button" class="btn btn-primary" title="Chỉnh sửa">                      
                                                                            <i class="ri-edit-line align-middle"></i>
                                                                    </button>
                                                                </a>
                                                            
                                                                <!-- Modal Chỉnh sửa -->
                                                                <div class="modal fade" id="editModal-{{ $banner->id }}" tabindex="-1"
                                                                    aria-labelledby="editModalLabel-{{ $banner->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel-{{ $banner->id }}">
                                                                                    Chỉnh sửa Banner</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="{{ route('banner.update', ['token' => auth()->user()->refesh_token, 'id' => $banner->id]) }}" method="POST" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('PUT') <!-- Sử dụng phương thức PUT để cập nhật -->
                                                                                    
                                                                                    <div class="row">
                                                                                        <!-- Title Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="title" class="form-label">Tiêu đề</label>
                                                                                                <input type="text" class="form-control" placeholder="Tiêu đề" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                        <!-- Content Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="content" class="form-label">Nội dung</label>
                                                                                                <textarea class="form-control" placeholder="Nội dung" id="content" name="content" rows="2" required>{{ old('content', $banner->content) }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Image Upload Field (Optional) -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="image" class="form-label">Hình ảnh</label>
                                                                                                <input type="file" class="form-control" id="image" name="image">
                                                                                                @if ($banner->image)
                                                                                                    <img src="{{ $banner->image }}" alt="Banner Image" style="max-width: 100px; margin-top: 10px;">
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="URL" class="form-label">Đường dẫn</label>
                                                                                                <input type="text" class="form-control" id="URL" value="{{ $banner->URL ?? ''}}" name="URL">

                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Status Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="status" class="form-label">Trạng thái</label>
                                                                                                <select style="width: 507px;left: 2rem;" class="form-control" id="status" name="status" required>
                                                                                                    <option value="2" {{ $banner->status == 2 ? 'selected' : '' }}>Active</option>
                                                                                                    <option value="3" {{ $banner->status == 3 ? 'selected' : '' }}>Inactive</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Index Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="index" class="form-label">Thứ tự</label>
                                                                                                <input type="number" class="form-control" id="index" name="index" placeholder="Thứ tự hiển thị" value="{{ old('index', $banner->index) }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Submit Button -->
                                                                                        <div class="col-lg-12">
                                                                                            <div class="text-end">
                                                                                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <li class="list-inline-item">
                                                                    <a 
                                                                        href="{{ route('changeStatusBanner', [
                                                                                                            'token' => auth()->user()->refesh_token,
                                                                                                            'id' => $banner->id,
                                                                                                            'status' => 3,
                                                                                                            'tab'=>1
                                                                                                            ]) }}"
                                                                    >
                                                                        <button type="button" class="btn btn-warning waves-effect waves-light" title="tắt">
                                                                            <i class="ri-lock-line align-middle"></i>
                                                                        </button>
                                                                    </a>
                                                                </li>
                                                                
                                                            
                                                               
                                                            </td>
                                                            



                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>


                                    </div>
                                </div>
                                <div class="mt-3">
                                    {{-- {{ $Posts->appends(['token' => auth()->user()->refesh_token])->links() }} --}}
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
                                <h4 class="card-title mb-0 flex-grow-1">Không hoạt động</h4>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-nowrap mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">tiêu đề</th>
                                                    <th scope="col">nội dung</th>
                                                    <th scope="col">Hình ảnh</th>
                                                    <th scope="col">Đường dẫn</th>
                                                    <th scope="col">Trạng thái</th>
                                                    <th scope="col">Vị trí</th>
                                                    <th scope="col">Người tạo</th>
                                                    <th scope="col">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                
                                                @foreach ($banners0ff as $banner)
                                                    <tr>
                                                 
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $banner->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $banner->title }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ \Illuminate\Support\Str::limit($banner->content, 150, '...') }}
                                                        </td>
                                                        <td style="max-width: 100px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            <img src="{{ $banner->image }}" alt="Post Image" style="max-width: 50%; height: auto;">
                                                        </td>
                                                         <td style="max-width: 200px; height: 100px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ \Illuminate\Support\Str::limit($banner->URL, 150, '...') }}
                                                        </td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            @if($banner->status == 2)
                                                            Hoạt động
                                                        @elseif($banner->status == 3)
                                                            Không hoạt động
                                                        @endif
                                                        </td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $banner->index }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $banner->user->fullname }}</td>
                                                            <td>
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $banner->id }}">
                                                                    <button type="button" class="btn btn-primary" title="Chỉnh sửa">
                                                                        <i class="ri-edit-line align-middle"></i>
                                                                    </button>
                                                                </a>
                                                            
                                                                <!-- Modal Chỉnh sửa -->
                                                                <div class="modal fade" id="editModal-{{ $banner->id }}" tabindex="-1"
                                                                    aria-labelledby="editModalLabel-{{ $banner->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel-{{ $banner->id }}">
                                                                                    Chỉnh sửa banner</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="{{ route('banner.update', ['token' => auth()->user()->refesh_token, 'id' => $banner->id,'tab'=>2]) }}" method="POST" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('PUT') <!-- Sử dụng phương thức PUT để cập nhật -->
                                                                                    
                                                                                    <div class="row">
                                                                                        <!-- Title Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="title" class="form-label">Tiêu đề</label>
                                                                                                <input type="text" class="form-control" placeholder="Tiêu đề" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                        <!-- Content Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="content" class="form-label">Nội dung</label>
                                                                                                <textarea class="form-control" placeholder="Nội dung" id="content" name="content" rows="2" required>{{ old('content', $banner->content) }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Image Upload Field (Optional) -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="image" class="form-label">Hình ảnh</label>
                                                                                                <input type="file" class="form-control" id="image" name="image">
                                                                                                @if ($banner->image)
                                                                                                    <img src="{{ $banner->image }}" alt="Banner Image" style="max-width: 100px; margin-top: 10px;">
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="URL" class="form-label">Đường dẫn</label>
                                                                                                <input type="text" class="form-control" id="URL" value="{{ $banner->URL ?? ''}}" name="URL">

                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Status Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="status" class="form-label">Trạng thái</label>
                                                                                                <select style="width: 507px;left: 2rem;" class="form-control" id="status" name="status" required>
                                                                                                    <option value="2" {{ $banner->status == 2 ? 'selected' : '' }}>Active</option>
                                                                                                    <option value="3" {{ $banner->status == 3 ? 'selected' : '' }}>Inactive</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Index Field -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="index" class="form-label">Thứ tự</label>
                                                                                                <input type="number" class="form-control" id="index" name="index" placeholder="Thứ tự hiển thị" value="{{ old('index', $banner->index) }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Submit Button -->
                                                                                        <div class="col-lg-12">
                                                                                            <div class="text-end">
                                                                                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <a 
                                                                href="{{ route('changeStatusBanner', [
                                                                    'token' => auth()->user()->refesh_token,
                                                                    'id' => $banner->id,
                                                                    'status' => 2,
                                                                    'tab'=>2
                                                                    ]) }}"
                                                                >
                                                                <button type="button" class="btn btn-success" title="Bật">
                                                                    <i class="ri-check-line align-middle"></i>
                                                                </button>
                                                                </a>
                                                            
                                                               
                                                            </td>
                                                            



                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>


                                    </div>
                                </div>
                                <div class="mt-3">
                                    {{-- {{ $Posts->appends(['token' => auth()->user()->refesh_token])->links() }} --}}
                                </div>
                            </div><!-- end card-body -->

                        </div><!-- end card -->
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection

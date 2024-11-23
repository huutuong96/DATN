@extends('index')
@section('title', 'List rank')

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
                        aria-selected="{{ $tab == 1 ? 'true' : 'false' }}">Tất cả</button>
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
                                <h4 class="card-title mb-0 flex-grow-1">Tất cả rank</h4>
                                <!-- Toggle Between Modals -->
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#firstmodal">Thêm rank</button>
                                <!-- First modal dialog -->
                                <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..."
                                    tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <form action="{{ route('rankCreate', ['token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <!-- Tiêu đề rank -->
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="title" class="form-label">Tiêu đề rank</label>
                                                                <input type="text" class="form-control" placeholder="Nhập tiêu đề" id="title" name="title" required>
                                                            </div>
                                                        </div>
                                                
                                                        <!-- Mô tả -->
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="description" class="form-label">Mô tả</label>
                                                                <input type="text" class="form-control" placeholder="Nhập Mô tả" id="description" name="description" required>
                                                            </div>
                                                        </div>
                                                
                                                        <!-- Số điểm áp dụng -->
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="condition" class="form-label">Số điểm áp dụng</label>
                                                                <input type="text" class="form-control" placeholder="Nhập Số điểm áp dụng" id="condition" name="condition" required>
                                                            </div>
                                                        </div>
                                                
                                                      
                                                
                                                        <!-- Tỷ lệ (%) -->
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="value" class="form-label">Tỷ lệ (%)</label>
                                                                <input type="number" step="0.01" class="form-control" placeholder="Nhập tỷ lệ" id="value" name="value" required>
                                                            </div>
                                                        </div>
                                                
                                                        <!-- Giá trị giới hạn -->
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="limitValue" class="form-label">Số tiền giới hạn</label>
                                                                <input type="number" step="0.01" class="form-control" placeholder="Nhập giá trị giới hạn" id="limitValue" name="limitValue" required>
                                                            </div>
                                                        </div>
                                                          <!-- Trạng thái -->
                                                          <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Trạng thái</label>
                                                                <select style="width: 507px;left: 2rem;" style="width: 130px" class="form-control" id="status" name="status" required>
                                                                    <option value="" disabled selected>Chọn trạng thái</option>
                                                                    <option value="2">Active</option>
                                                                    <option value="0">Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                
                                                        <!-- Submit -->
                                                        <div class="col-lg-12">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-primary">Thêm rank</button>
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
                                                    <th scope="col">Mô tả</th>
                                                    <th scope="col">Số điểm áp dụng</th>
                                                    <th scope="col">phầm trăm rank</th>
                                                    <th scope="col">Số tiền tối đa được giảm</th>
                                                    <th scope="col">trạng thái</th>
                                                    <th scope="col">Người tạo</th>
                                                    <th scope="col">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($ranks as $rank)
                                                    <tr>
                                                        {{-- @dd($voucherMain->user->fullname); --}}
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $rank->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $rank->title }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $rank->description }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $rank->condition }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $rank->value * 100 }}%</td>
                                                        <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ number_format($rank->limitValue) }}VNĐ</td>
                                                        
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            @if($rank->status == 2)
                                                            Hoạt động
                                                        @elseif($rank->status == 0)
                                                            Không hoạt động
                                                        @endif</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                           
                                                            {{ $rank->user->fullname  }}
                                                            </td>
                                                        <td>
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editModal-{{ $rank->id }}">
                                                                <button type="button" class="btn btn-primary"
                                                                    title="Chỉnh sửa">
                                                                        <i class="ri-edit-line align-middle"></i>
                                                                </button>
                                                            </a>

                                                            <!-- Modal Chỉnh sửa -->
                                                            <div class="modal fade" id="editModal-{{ $rank->id }}" tabindex="-1"
                                                                    aria-labelledby="editModalLabel-{{ $rank->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel-{{ $rank->id }}">
                                                                                    Chỉnh sửa Voucher</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="{{ route('rank.update', ['token' => auth()->user()->refesh_token, 'id' => $rank->id]) }}" method="POST" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('PUT')
                                                                                    <div class="row">
                                                                                        <!-- Tiêu đề -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="title-{{ $rank->id }}" class="form-label">Tiêu đề</label>
                                                                                                <input type="text" class="form-control" placeholder="Tiêu đề" id="title-{{ $rank->id }}" name="title" value="{{ $rank->title }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Mô tả -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="description-{{ $rank->id }}" class="form-label">Mô tả</label>
                                                                                                <input type="text" class="form-control" placeholder="Mô tả" id="description-{{ $rank->id }}" name="description" value="{{ $rank->description }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Điều kiện áp dụng -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="condition-{{ $rank->id }}" class="form-label">Điều kiện áp dụng</label>
                                                                                                <input type="number" class="form-control" placeholder="Điều kiện áp dụng" id="condition-{{ $rank->id }}" name="condition" value="{{ $rank->condition }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Tỷ lệ (%) -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="value-{{ $rank->id }}" class="form-label">Tỷ lệ (%)</label>
                                                                                                <input type="number" step="0.01" class="form-control" placeholder="Tỷ lệ (%)" id="value-{{ $rank->id }}" name="value" value="{{ $rank->value }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Giá trị giới hạn -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="limitValue-{{ $rank->id }}" class="form-label">Giá trị giới hạn</label>
                                                                                                <input type="number" step="0.01" class="form-control" placeholder="Giá trị giới hạn" id="limitValue-{{ $rank->id }}" name="limitValue" value="{{ $rank->limitValue }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Trạng thái -->
                                                                                        <div class="col-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="status-{{ $rank->id }}" class="form-label">Trạng thái</label>
                                                                                                <select class="form-control" id="status-{{ $rank->id }}" name="status" required>
                                                                                                    <option value="" disabled>Chọn trạng thái</option>
                                                                                                    <option value="2" {{ $rank->status == 2 ? 'selected' : '' }}>Active</option>
                                                                                                    <option value="0" {{ $rank->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Nút lưu -->
                                                                                        <div class="col-lg-12">
                                                                                            <div class="text-end">
                                                                                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
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
                                                                        href="{{ route('changeStatusRank', [
                                                                                                            'token' => auth()->user()->refesh_token,
                                                                                                            'id' => $rank->id,
                                                                                                            'status' => 0,
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

                    <div class="tab-pane fade {{ $tab == 2 ? 'show active' : '' }}" id="nav-profile" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1"> rank đã tắt</h4>

                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-nowrap mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">tiêu đề</th>
                                                    <th scope="col">Mô tả</th>
                                                    <th scope="col">Số điểm áp dụng</th>
                                                    <th scope="col">phầm trăm rank</th>
                                                    <th scope="col">Số tiền tối đa được giảm</th>
                                                    <th scope="col">trạng thái</th>
                                                    <th scope="col">Người tạo</th>
                                                    <th scope="col">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($ranks0ff as $rank)
                                                    <tr>
                                                        {{-- @dd($voucherMain->user->fullname); --}}
                                                        <th scope="row"><a href="#"
                                                            class="fw-medium">{{ $rank->id }}</a></th>
                                                    <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $rank->title }}</td>
                                                    <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $rank->description }}</td>
                                                    <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $rank->condition }}</td>
                                                    <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $rank->value * 100 }}%</td>
                                                    <td
                                                    style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ number_format($rank->limitValue) }}VNĐ</td>
                                                    
                                                    <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                        @if($rank->status == 2)
                                                        Hoạt động
                                                    @elseif($rank->status == 0)
                                                        Không hoạt động
                                                    @endif</td>
                                                    <td
                                                        style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                       
                                                        {{ $rank->user->fullname  }}
                                                        </td>
                                                    <td>
                                                        <a href="#" data-bs-toggle="modal"
                                                            data-bs-target="#editModal-{{ $rank->id }}">
                                                            <button type="button" class="btn btn-primary"
                                                                title="Chỉnh sửa">
                                                                    <i class="ri-edit-line align-middle"></i>
                                                            </button>
                                                        </a>

                                                        <!-- Modal Chỉnh sửa -->
                                                        <div class="modal fade" id="editModal-{{ $rank->id }}" tabindex="-1"
                                                                aria-labelledby="editModalLabel-{{ $rank->id }}" aria-hidden="true">
                                                                <div class="modal-dialog modal-xl">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="editModalLabel-{{ $rank->id }}">
                                                                                Chỉnh sửa Voucher</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="{{ route('rank.update', ['token' => auth()->user()->refesh_token, 'id' => $rank->id, 'tab'=>2]) }}" method="POST" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <div class="row">
                                                                                    <!-- Tiêu đề -->
                                                                                    <div class="col-6">
                                                                                        <div class="mb-3">
                                                                                            <label for="title-{{ $rank->id }}" class="form-label">Tiêu đề</label>
                                                                                            <input type="text" class="form-control" placeholder="Tiêu đề" id="title-{{ $rank->id }}" name="title" value="{{ $rank->title }}" required>
                                                                                        </div>
                                                                                    </div>
                                                                            
                                                                                    <!-- Mô tả -->
                                                                                    <div class="col-6">
                                                                                        <div class="mb-3">
                                                                                            <label for="description-{{ $rank->id }}" class="form-label">Mô tả</label>
                                                                                            <input type="text" class="form-control" placeholder="Mô tả" id="description-{{ $rank->id }}" name="description" value="{{ $rank->description }}" required>
                                                                                        </div>
                                                                                    </div>
                                                                            
                                                                                    <!-- Điều kiện áp dụng -->
                                                                                    <div class="col-6">
                                                                                        <div class="mb-3">
                                                                                            <label for="condition-{{ $rank->id }}" class="form-label">Điều kiện áp dụng</label>
                                                                                            <input type="number" class="form-control" placeholder="Điều kiện áp dụng" id="condition-{{ $rank->id }}" name="condition" value="{{ $rank->condition }}" required>
                                                                                        </div>
                                                                                    </div>
                                                                            
                                                                                    <!-- Tỷ lệ (%) -->
                                                                                    <div class="col-6">
                                                                                        <div class="mb-3">
                                                                                            <label for="value-{{ $rank->id }}" class="form-label">Tỷ lệ (%)</label>
                                                                                            <input type="number" step="0.01" class="form-control" placeholder="Tỷ lệ (%)" id="value-{{ $rank->id }}" name="value" value="{{ $rank->value }}" required>
                                                                                        </div>
                                                                                    </div>
                                                                            
                                                                                    <!-- Giá trị giới hạn -->
                                                                                    <div class="col-6">
                                                                                        <div class="mb-3">
                                                                                            <label for="limitValue-{{ $rank->id }}" class="form-label">Giá trị giới hạn</label>
                                                                                            <input type="number" step="0.01" class="form-control" placeholder="Giá trị giới hạn" id="limitValue-{{ $rank->id }}" name="limitValue" value="{{ $rank->limitValue }}" required>
                                                                                        </div>
                                                                                    </div>
                                                                            
                                                                                    <!-- Trạng thái -->
                                                                                    <div class="col-6">
                                                                                        <div class="mb-3">
                                                                                            <label for="status-{{ $rank->id }}" class="form-label">Trạng thái</label>
                                                                                            <select class="form-control" id="status-{{ $rank->id }}" name="status" required>
                                                                                                <option value="" disabled>Chọn trạng thái</option>
                                                                                                <option value="2" {{ $rank->status == 2 ? 'selected' : '' }}>Active</option>
                                                                                                <option value="0" {{ $rank->status == 0 ? 'selected' : '' }}>Inactive</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                            
                                                                                    <!-- Nút lưu -->
                                                                                    <div class="col-lg-12">
                                                                                        <div class="text-end">
                                                                                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                            

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                               
                                                                    <a 
                                                                    href="{{ route('changeStatusRank', [
                                                                        'token' => auth()->user()->refesh_token,
                                                                        'id' => $rank->id,
                                                                        'status' => 2,
                                                                        'tab'=>2
                                                                        ]) }}"
                                                                    >
                                                                    <button type="button" class="btn btn-success" title="Bật">
                                                                        <i class="ri-check-line align-middle"></i>
                                                                    </button>
                                                                    </a>
                                                               
                                                                <form
                                                                action="{{ route('rank.delete', ['token' => auth()->user()->refesh_token, 'id' => $rank->id, 'tab' => 2]) }}" method="POST" style="display: inline;"
                                                                >
                                                                @csrf
                                                                @method('DELETE')                                                       
                                                                    <button type="submit" class="btn btn-danger" title="Xóa"  onclick="return confirm('Bạn có chắc chắn muốn xóa rank này?');">
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
                                    {{-- {{ $Posts->appends(['token' => auth()->user()->refesh_token])->links() }} --}}
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

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
                                <h4 class="card-title mb-0 flex-grow-1">Tất cả phương thức</h4>
                                <!-- Toggle Between Modals -->
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#firstmodal">Thêm phương thức</button>
                                <!-- First modal dialog -->
                                <div class="modal fade" id="firstmodal" aria-hidden="true" aria-labelledby="..."
                                    tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-body text-center p-5">
                                                <form action="{{ route('storepaymant', ['token' => auth()->user()->refesh_token]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="name" class="form-label">Tên Phương Thức <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" placeholder="Nhập tên phương thức" id="name" name="name" maxlength="255" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="code" class="form-label">Mã Phương Thức <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" placeholder="Nhập mã phương thức (ví dụ: PAYPAL, VN_PAY)" id="code" name="code" maxlength="50" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label for="description" class="form-label">Mô tả</label>
                                                                <textarea class="form-control" placeholder="Mô tả chi tiết về phương thức thanh toán" id="description" name="description" rows="4" maxlength="500"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                                                                <select style="width: 507px;left: 2rem;" class="form-control mt-3" id="status" name="status" required>
                                                                    <option value="" disabled selected>Chọn trạng thái</option>
                                                                    <option value="1">Kích hoạt</option>
                                                                    <option value="0">Không kích hoạt</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        {{-- <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="settings" class="form-label">Thiết Lập (Tùy Chọn)</label>
                                                                <input type="text" class="form-control" placeholder="Nhập thiết lập (nếu có)" id="settings" name="settings" maxlength="255">
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-12 mt-5">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-primary">Thêm Phương Thức</button>
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
                                                    <th scope="col">Tên Phương Thức</th>
                                                    <th scope="col">Mã Phương Thức</th>
                                                    <th scope="col">Mô Tả</th>
                                                    <th scope="col">Trạng Thái</th>
                                                    <th scope="col">Hành Động</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($payment_method as $payment)
                                                    <tr>
                                                        {{-- @dd($voucherMain->user->fullname); --}}
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $payment->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $payment->name }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $payment->code }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $payment->description }}</td>
            
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            @if($payment->status == 1)
                                                            Hoạt động
                                                        @elseif($payment->status == 0)
                                                            Không hoạt động
                                                        @endif
                                                    </td>
                                                    <td>
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editModal-{{ $payment->id }}">
                                                                <button type="button" class="btn btn-primary"
                                                                    title="Chỉnh sửa">
                                                                        <i class="ri-edit-line align-middle"></i>
                                                                </button>
                                                            </a>

                                                            <!-- Modal Chỉnh sửa -->
                                                            <div class="modal fade" id="editModal-{{ $payment->id }}" tabindex="-1"
                                                                    aria-labelledby="editModalLabel-{{ $payment->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel-{{ $payment->id }}">
                                                                                    Chỉnh sửa Voucher</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="{{ route('updatepayment', ['token' => auth()->user()->refesh_token, 'id' => $payment->id]) }}" method="POST" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('PUT') <!-- Sử dụng PUT để cập nhật -->
                                                                                
                                                                                    <div class="row">
                                                                                        <!-- Payment Method Name -->
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="name" class="form-label">Tên Phương Thức <span class="text-danger">*</span></label>
                                                                                                <input type="text" class="form-control" id="name" name="name" 
                                                                                                       placeholder="Nhập tên phương thức" value="{{ old('name', $payment->name) }}" maxlength="255" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Payment Method Code -->
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="code" class="form-label">Mã Phương Thức <span class="text-danger">*</span></label>
                                                                                                <input type="text" class="form-control" id="code" name="code" 
                                                                                                       placeholder="Nhập mã phương thức (ví dụ: PAYPAL, VN_PAY)" value="{{ old('code', $payment->code) }}" maxlength="50" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Description -->
                                                                                        <div class="col-12">
                                                                                            <div class="mb-3">
                                                                                                <label for="description" class="form-label">Mô tả</label>
                                                                                                <textarea class="form-control" id="description" name="description" 
                                                                                                          placeholder="Mô tả chi tiết về phương thức thanh toán" rows="4" maxlength="500">{{ old('description', $payment->description) }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Status -->
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                                                                                                <select style="width: 507px;left: 2rem;" class="form-control mt-3" id="status" name="status" required>
                                                                                                    <option value="" disabled>Chọn trạng thái</option>
                                                                                                    <option value="1" {{ old('status', $payment->status) == 1 ? 'selected' : '' }}>Kích hoạt</option>
                                                                                                    <option value="0" {{ old('status', $payment->status) == 0 ? 'selected' : '' }}>Không kích hoạt</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 mt-4">
                                                                                            <div class="text-end">
                                                                                                <button type="submit" class="btn btn-primary">Cập nhật Phương Thức</button>
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
                                                                        href="{{ route('changeStatuspayment', [
                                                                                                            'token' => auth()->user()->refesh_token,
                                                                                                            'id' => $payment->id,
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
                                <h4 class="card-title mb-0 flex-grow-1"> Phương thức đã tắt</h4>

                            </div><!-- end card header -->
                            <div class="card-body">
                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-nowrap mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Tên Phương Thức</th>
                                                    <th scope="col">Mã Phương Thức</th>
                                                    <th scope="col">Mô Tả</th>
                                                    <th scope="col">Trạng Thái</th>
                                                    <th scope="col">Hành Động</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($payment_method0ff as $payment)
                                                    <tr>
                                                        {{-- @dd($voucherMain->user->fullname); --}}
                                                        <th scope="row"><a href="#"
                                                                class="fw-medium">{{ $payment->id }}</a></th>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $payment->name }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $payment->code }}</td>
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $payment->description }}</td>
            
                                                        <td
                                                            style="max-width: 150px; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                                                            @if($payment->status == 1)
                                                            Hoạt động
                                                        @elseif($payment->status == 0)
                                                            Không hoạt động
                                                        @endif
                                                    </td>
                                                    <td>
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editModal-{{ $payment->id }}">
                                                                <button type="button" class="btn btn-primary"
                                                                    title="Chỉnh sửa">
                                                                        <i class="ri-edit-line align-middle"></i>
                                                                </button>
                                                            </a>

                                                            <!-- Modal Chỉnh sửa -->
                                                            <div class="modal fade" id="editModal-{{ $payment->id }}" tabindex="-1"
                                                                    aria-labelledby="editModalLabel-{{ $payment->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel-{{ $payment->id }}">
                                                                                    Chỉnh sửa Voucher</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form action="{{ route('updatepayment', ['token' => auth()->user()->refesh_token, 'id' => $payment->id]) }}" method="POST" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('PUT') <!-- Sử dụng PUT để cập nhật -->
                                                                                
                                                                                    <div class="row">
                                                                                        <!-- Payment Method Name -->
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="name" class="form-label">Tên Phương Thức <span class="text-danger">*</span></label>
                                                                                                <input type="text" class="form-control" id="name" name="name" 
                                                                                                       placeholder="Nhập tên phương thức" value="{{ old('name', $payment->name) }}" maxlength="255" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Payment Method Code -->
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="code" class="form-label">Mã Phương Thức <span class="text-danger">*</span></label>
                                                                                                <input type="text" class="form-control" id="code" name="code" 
                                                                                                       placeholder="Nhập mã phương thức (ví dụ: PAYPAL, VN_PAY)" value="{{ old('code', $payment->code) }}" maxlength="50" required>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Description -->
                                                                                        <div class="col-12">
                                                                                            <div class="mb-3">
                                                                                                <label for="description" class="form-label">Mô tả</label>
                                                                                                <textarea class="form-control" id="description" name="description" 
                                                                                                          placeholder="Mô tả chi tiết về phương thức thanh toán" rows="4" maxlength="500">{{ old('description', $payment->description) }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                
                                                                                        <!-- Status -->
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                                                                                                <select style="width: 507px;left: 2rem;" class="form-control mt-3" id="status" name="status" required>
                                                                                                    <option value="" disabled>Chọn trạng thái</option>
                                                                                                    <option value="1" {{ old('status', $payment->status) == 1 ? 'selected' : '' }}>Kích hoạt</option>
                                                                                                    <option value="0" {{ old('status', $payment->status) == 0 ? 'selected' : '' }}>Không kích hoạt</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 mt-4">
                                                                                            <div class="text-end">
                                                                                                <button type="submit" class="btn btn-primary">Cập nhật Phương Thức</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                                
                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                               
                                                                    <a 
                                                                        href="{{ route('changeStatuspayment', [
                                                                                                            'token' => auth()->user()->refesh_token,
                                                                                                            'id' => $payment->id,
                                                                                                            'status' => 1,
                                                                                                            'tab'=>2
                                                                                                            ]) }}"
                                                                    >
                                                                    <button type="button" class="btn btn-success" title="Bật">
                                                                        <i class="ri-check-line align-middle"></i>
                                                                    </button>
                                                                    </a>
                                                            
                                                             
                                                                <form action="{{ route('destroypayment', ['token' => auth()->user()->refesh_token, 'id' => $payment->id, 'tab' => 2]) }}" method="POST" style="display: inline;"   >
                                                                @csrf
                                                                @method('DELETE')                                                       
                                                                    <button type="submit" class="btn btn-danger" title="Xóa"  onclick="return confirm('Bạn có chắc chắn muốn xóa payment này?');">
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

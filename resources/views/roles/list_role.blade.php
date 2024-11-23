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
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Tên</label>
                                                    <input name="title" type="text" class="form-control" placeholder="Tên Quyền hạn" id="title" required>
                                                </div><!--end mb-3-->
                                            </div><!--end col-->

                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="index" class="form-label">Mô tả</label>
                                                    <input name="description" type="text" class="form-control"placeholder="Thêm mô tả" value="" id="index" required>
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
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($roles as $role)
                                    <tr>
                                        <th scope="row"><a href="#" class="fw-medium">{{$role->id}}</a></th>
                                        <td>{{$role->title}}</td>
                                        <td>{{$role->description ?? "Không có mô tả"}}</td>
                                        <td>
                                            <ul class="list-inline">                                   
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('list_permission', ['token' => auth()->user()->refesh_token,
                                                                                        'id' => $role->id]) }}">
                                                       <button type="button" class="btn btn-secondary" title="Cấp quyền">
                                                        <i class="ri-shield-user-line align-middle"></i>
                                                    </button>
                                                    
                                                    </a>
                                                </li>

                                                @if ($role->status == 2)
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('change_role', [
                                                                                                'token' => auth()->user()->refesh_token,
                                                                                                'id' => $role->id,
                                                                                                'status' => 3,
                                                                                                ]) }}">
                                                       <button type="button" class="btn btn-warning" title="Tắt">
                                                        <i class="ri-close-line align-middle"></i>
                                                    </button>
                                                    
                                                    </a>
                                                </li>
                                                @elseif ($role->status == 3)
                                                <li class="list-inline-item">
                                                    <a
                                                        href="{{ route('change_role', [
                                                                                            'token' => auth()->user()->refesh_token,
                                                                                            'id' => $role->id,
                                                                                            'status' => 2,
                                                                                            ]) }}">
                                                        <button type="button" class="btn btn-primary" title="Bật">
                                                            <i class="ri-power-line align-middle"></i>
                                                        </button>
                                                        


                                                </li>
                                                @endif
                                                <li class="list-inline-item">
                                                    <!-- Toggle Between Modals -->
                                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" title="chỉnh sửa" data-bs-target="#firstmodal{{ $role->id }}"><i class="ri-edit-line align-middle"></i></button>
                                                    <!-- First modal dialog -->
                                                    <div class="modal fade" id="firstmodal{{ $role->id }}" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-body text-center p-5">
                                                                <div class="row">
                                                                    <form action="{{ route('role_update', ['token' => auth()->user()->refesh_token, 'id' => $role->id]) }}" method="POST">
                                                                        @csrf
                                                                        @method('put')
                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="title" class="form-label">Tên</label>
                                                                                    <input name="title" type="text" class="form-control" placeholder="Tên Quyền hạn" value="{{$role->title}}" id="title" required>
                                                                                </div><!--end mb-3-->
                                                                            </div><!--end col-->

                                                                            <div class="col-12">
                                                                                <div class="mb-3">
                                                                                    <label for="index" class="form-label">Mô tả</label>
                                                                                    <input name="description" type="text" class="form-control"placeholder="Thêm mô tả" value="{{$role->description}}" id="index" required>
                                                                                </div><!--end mb-3-->
                                                                            </div><!--end col-->

                                                                            <div class="col-12">
                                                                                <label for="index" class="form-label">Trạng thái</label>
                                                                                @if ($role->status == 2)
                                                                                <select name="status" class="form-select mb-3" aria-label="Default select example">
                                                                                    <option selected value="2">Hoạt động </option>
                                                                                    <option value="3">Tạm ngưng</option>
                                                                                </select>
                                                                                @elseif ($role->status == 3)
                                                                                <select name="status" class="form-select mb-3" aria-label="Default select example">
                                                                                    <option value="2">Hoạt động </option>
                                                                                    <option selected value="3">Tạm ngưng</option>
                                                                                </select>
                                                                                @endif
                                                                            </div>
                                 
                                                                            <div class="col-lg-12">
                                                                                <div class="text-end">
                                                                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                                                </div><!--end text-end-->
                                                                            </div><!--end col-->
                                                                        </div><!--end row-->
                                                                    </form>
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
                                                         <button type="button" class="btn btn-danger" title="Xóa"  onclick="return confirm('Bạn có chắc chắn muốn xóa quyền này?');">
                                                            <i class="ri-delete-bin-line align-middle"></i>
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
@extends('index')

@section('title' , 'Setting Admin')


@section('main')

<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cài đặt hệ thống</h5>
                </div>
                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 10px;">
                                    <div class="form-check">
                                        <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                                    </div>
                                </th>
                                <th data-ordering="false">Chức năng</th>
                                <th data-ordering="false">Mô Tả</th>
                                <th data-ordering="false">Người thực hiện</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <div class="form-check">
                                        <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                    </div>
                                </th>
                                <td class="col-2">Dữ liệu </td>
                                <td class="col-7">Xóa tất cả dữ liệu</td>
                                <td class="col-2">VLZ1400087402</td>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="">
                                            <li>
                                                <a class="dropdown-item remove-item-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop1">
                                                    <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Xóa
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <div class="form-check">
                                        <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                    </div>
                                </th>
                                <td class="col-2">Dữ liệu </td>
                                <td class="col-7">Xóa dữ liệu theo ngày</td>
                                <td class="col-2">VLZ1400087402</td>
                                <td>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-fill align-middle"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="">
                                            <li>
                                                <a class="dropdown-item remove-item-btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">
                                                    <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Xóa
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <h6>Thêm chức năng hệ thống vui lòng liên hệ admin</h6>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--end col-->
    </div>

</div>

<!-- CATEGORIES -->
<div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <lord-icon
                    src="https://cdn.lordicon.com/lupuorrc.json"
                    trigger="loop"
                    colors="primary:#121331,secondary:#08a88a"
                    style="width:120px;height:120px">
                </lord-icon>
                    <form action="{{ route('delete_all', ['token' => auth()->user()->refesh_token]) }}" method="post">
                        @csrf
                        <div class="mt-4">
                            <h4 class="mb-3">Xóa Tất Dữ Liệu!</h4>
                            <p class="text-muted mb-4">Xóa tất Dữ Liệu. (Không xóa những dữ liệu define)</p>
                            <div class="col-xxl-12 mb-4">
                                <input name="email" type="email" class="form-control" placeholder="Nhập Email của bạn" required>
                            </div>
                            <div class="col-xxl-12 mb-4">
                                <input name="password" type="password" class="form-control" placeholder="Mật khẩu của bạn" required>
                            </div>
                            <div class="hstack gap-2 justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-link link-success fw-medium material-shadow-none" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Đóng</a>
                                <button type="submit" class="btn btn-success">Thực hiện</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <lord-icon
                    src="https://cdn.lordicon.com/lupuorrc.json"
                    trigger="loop"
                    colors="primary:#121331,secondary:#08a88a"
                    style="width:120px;height:120px">
                </lord-icon>
                    <form action="{{ route('delete_data_by_date', ['token' => auth()->user()->refesh_token]) }}" method="post">
                        @csrf
                        <div class="mt-4">
                            <h4 class="mb-3">Xóa tất Dữ Liệu Theo Ngày!</h4>
                            <p class="text-muted mb-4">Xóa tất Dữ Liệu Theo Ngày. (Không xóa những dữ liệu define)</p>
                            <div class="col-xxl-12 mb-4">
                                <input name="email" type="email" class="form-control" placeholder="Nhập Email của bạn" required>
                            </div>
                            <div class="col-xxl-12 mb-4">
                                <input name="password" type="password" class="form-control" placeholder="Mật khẩu của bạn" required>
                            </div>
                            <div class="col-xxl-12 mb-4">
                                <label for="start_date">Xóa từ ngày</label>
                                <input name="start_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-xxl-12 mb-4">
                                <label for="end_date">Đến ngày</label>
                                <input name="end_date" type="date" class="form-control" required>
                            </div>
                            <div class="hstack gap-2 justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-link link-success fw-medium material-shadow-none" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Đóng</a>
                                <button type="submit" class="btn btn-success">Thực hiện</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>



@endsection
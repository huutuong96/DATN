@extends('index')

@section('title' , 'Modifier')


@section('main')

<div class="container-fluid">

                <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Danh sách chương trình khuyến mãi</h5>
                                </div>
                                <div class="col-2 p-3">
                                    <button type="button" class="btn btn-secondary btn-animation waves-effect waves-light col-12" data-bs-toggle="modal" data-bs-target="#myModal">Thêm mới</button>
                                </div>

                                <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Thêm chương trình khuyến mãi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                            <form class="row g-3" action="{{route('create_modifier', ['token' => auth()->user()->refesh_token])}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="col-md-2">
                                                    <label for="validationDefault01" class="form-label">Sub domain</label>
                                                    <input name="sub_domain" type="text" class="form-control" id="validationDefault01" value="" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="validationDefault01" class="form-label">Mô tả</label>
                                                    <input name="descriptions" type="text" class="form-control" id="validationDefault01" value="" >
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="validationDefault01" class="form-label">Từ Ngày</label>
                                                    <input name="descriptions" type="date" class="form-control" id="validationDefault01" value="" >
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="validationDefault01" class="form-label">Đến Ngày</label>
                                                    <input name="descriptions" type="date" class="form-control" id="validationDefault01" value="" >
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="validationDefault01" class="form-label">Giao Diện</label>
                                                    <img src="#" alt="">
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                                                        <select style="width: 507px;left: 2rem;" class="form-control mt-3" name="status" required>
                                                            <option value="" disabled selected>Chọn trạng thái</option>
                                                            <option value="1">Kích hoạt</option>
                                                            <option value="0">Không kích hoạt</option>
                                                        </select>
                                                    </div>
                                                </div>
                                              
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary ">Thêm mới</button>
                                            </div>
                                            </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                                <div class="card-body">
                                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 10px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                                                    </div>
                                                </th>
                                                <th data-ordering="false">ID</th>
                                                <th data-ordering="false">Chủ Đề</th>
                                                <th data-ordering="false">Áp dụng</th>
                                                <th data-ordering="false">Hiệu Lực</th>
                                                <!-- <th>Hành động</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($modifiers as $modi)
                                                <tr>
                                                    <th scope="row">
                                                        <div class="form-check">
                                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                                        </div>
                                                    </th>
                                                    <td>{{$modi->title}}</td>
                                                    <td>
                                                        Sản Phẩm Áp Dụng: <br>
                                                        Người Dùng Áp Dụng: <br>
                                                        Cửa Hàng Áp Dụng: <br>
                                                    </td>
                                                    <td>
                                                        Từ ngày: {{$modi->from}} <br>
                                                        Đến ngày: {{$modi->to}} 
                                                        Còn lại : 
                                                                <?php
                                                                $now = \Carbon\Carbon::now();
                                                                $end_date = \Carbon\Carbon::parse($modi->to);
                                                                $days_left = $end_date->diffInDays($now);
                                                                ?>
                                                                {{$days_left}} ngày
                                                    </td>
                                                    <td>Lượt xem: <br>
                                                        {{$modi->view}}</td>
                                                    <td>{{$modi->banner}}</td>
                                                    <td>Giảm giá: <br>
                                                        {{$modi->sale_price}}</td>
                                                    <td>% Giảm giá: <br>
                                                        {{$modi->rate}}</td>
                                                    <td>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <!-- <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li> -->
                                                                <li>
                                                                    <a class="dropdown-item remove-item-btn" href="{{route('delete_modifier', ['id' => $modi->id, 'token' => auth()->user()->refesh_token])}}">
                                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Xóa
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div>
             
                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div>

</div>


@endsection
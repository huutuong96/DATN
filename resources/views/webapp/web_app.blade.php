@extends('index')

@section('title' , 'Web App')


@section('main')

<div class="container-fluid">

<div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Danh sách web app</h5>
                                </div>
                                <div class="col-2 p-3">
                                    <button type="button" class="btn btn-secondary btn-animation waves-effect waves-light col-12" data-bs-toggle="modal" data-bs-target="#myModal">Thêm mới</button>
                                </div>

                                <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Thêm web app</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                            <form class="row g-3" action="{{route('create_app', ['token' => auth()->user()->refesh_token])}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="col-md-4">
                                                    <label for="validationDefault01" class="form-label">Tên</label>
                                                    <input name="name_app" type="text" class="form-control" id="validationDefault01" value="Tên app" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="validationDefault02" class="form-label">Icon</label>
                                                    <input name="icon_app" type="file" class="form-control" id="validationDefault02" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="validationDefaultUsername" class="form-label">Đường dẫn</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="inputGroupPrepend2">URL</span>
                                                        <input type="text" class="form-control" name="url_app"
                                                            required>
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
                                                <th data-ordering="false">Tên Wep App</th>
                                                <th data-ordering="false">Icon</th>
                                                <th data-ordering="false">Đường dẫn</th>
                                                <!-- <th>Hành động</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($apps as $app)
                                                <tr>
                                                    <th scope="row">
                                                        <div class="form-check">
                                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                                        </div>
                                                    </th>
                                                    <td>{{$app->id}}</td>
                                                    <td>{{$app->name}}</td>
                                                    <td class="col-2"><img class="col-4" src="{{$app->icon}}"></td>
                                                    <td><a target="_blank" href="{{$app->url}}">{{$app->url}}</a></td>
                                                    <td>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <!-- <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li> -->
                                                                <li>
                                                                    <a class="dropdown-item remove-item-btn" href="{{route('delete_app', ['id' => $app->id, 'token' => auth()->user()->refesh_token])}}">
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
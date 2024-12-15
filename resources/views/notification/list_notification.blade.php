@extends('index')

@section('title', 'Danh sách thông báo')

@section('main')

<div class="container-fluid">

<div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Danh sách thông báo</h5>
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
                                                <th data-ordering="false">ID</th>
                                                <th data-ordering="false">Tiêu đề</th>
                                                <th data-ordering="false">Hình ảnh</th>
                                                <th data-ordering="false">Mô tả</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($notificationMain as $noti)
                                                <tr>
                                                    <th scope="row">
                                                        <div class="form-check">
                                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                                        </div>
                                                    </th>
                                                    <td>{{$noti->id}}</td>
                                                    <td>{{$noti->title}}</td>
                                                    <td class="col-2"><img class="col-4" src="{{$noti->image}}"></td>
                                                    <td><a href="#!">{{$noti->description}}</a></td>
                                                    <td>
                                                        <a class="btn btn-soft-secondary btn-sm" href="{{route('delete_notification', ['id' => $noti->id, 'token' => auth()->user()->refesh_token])}}" >
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                        </a>
                                                    
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div>
                                        {{ $notificationMain->appends(['token' => auth()->user()->refesh_token])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div>

</div>

@endsection
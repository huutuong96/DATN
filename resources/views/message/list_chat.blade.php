@extends('index')
@section('title', 'Tin nhắn')

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
            
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Danh sách đoạn chat</h4>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div class="live-preview">
                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Cửa hàng</th>
                                                <th scope="col">Thời gian</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chats as $chat)
                                                <tr>
                                                    <th scope="row">{{ $chat->id }}</th>
                                                    <td>{{ $chat->shops->shop_name}}</td>
                                                    <td>{{ $chat->created_at->diffForHumans() }}</td>
                                                    <td>
                                                        <a href="{{route('chat',['mes_id' => $chat->id, 'shop_id' => $chat->shops->id ,'token' => auth()->user()->refesh_token])}}" class="link-success">Xem <i class="ri-arrow-right-line align-middle"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>

        </div>
    </div>


@endsection
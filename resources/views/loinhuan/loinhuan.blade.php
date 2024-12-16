@extends('index')
@section('title', 'List Store')
@section('link')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
  
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
@endsection
@section('main')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Thống kê lợi nhuận</h4>
                </div><!-- end card header -->
                <div class="card-body">
                <table id="shop" class="display" style="width:100%">
                                    <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col">Tên cửa hàng</th>
                                                    <th scope="col" style="width: 20%;">Địa chỉ</th>
                                                    <th scope="col">Chủ cửa hàng</th>
                                                    <th scope="col" style="width: 16%;">Trạng thái</th>
                                                    <th scope="col" style="width: 12%;">Doanh thu</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($listShop as $shop)
                                                @if($shop->fee == 0)
                                                    @continue
                                                @endif
                                                <tr>
                                                    <td><b>
                                                    {{$shop->shop_name}}
                                                    </b></td>
                                                    <td>{{$shop->pick_up_address}} <br> {{$shop->ward}} <br> {{$shop->district}} <br> {{$shop->province}}</td>
                                                    <td><img src="{{$shop->user[0]->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                                        <a href="#javascript: void(0);" class="text-body fw-medium">{{$shop->user[0]->fullname ?? null}}</a>
                                                    </td>
                                                    <td><span class="badge bg-success-subtle text-success p-2">Cửa hàng nổi bật</span></td>
                                                    <td>
                                                        <div class="text-nowrap">{{number_format($shop->fee)}}vnđ</div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                            </tbody><!-- end tbody -->
                                    </table>
                                    <script>
                                        new DataTable('#shop', {
                                            language: {   
                                                sEmptyTable: "Không có dữ liệu trong bảng",
                                                sProcessing: "Đang xử lý...",
                                                sLengthMenu: "Hiển thị _MENU_ mục",
                                                sZeroRecords: "Không tìm thấy dòng nào phù hợp",
                                                sInfo: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                                                sInfoEmpty: "Hiển thị 0 đến 0 của 0 mục",
                                                sInfoFiltered: "(lọc từ _MAX_ mục)",
                                                sSearch: "Tìm:",
                                            }
                                        });
                                    </script>
                </div><!-- end card -->
            </div> 
        </div>
    </div>
</div>


@endsection

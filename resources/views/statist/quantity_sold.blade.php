@extends('index')
@section('title', 'Tổng quan')
@section('link')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
  
  <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
@endsection
@section('main')


<div class="container-fluid">

    <div class="row">
    <div style="display: flex; justify-content: space-between;">
        <h5>Thống kê số lượng bán ra Tháng {{ \Carbon\Carbon::now()->format('m') }}</h5>
        <h5 class="me-4">Top 10 cửa hàng theo lượng bán ra</h5>
    </div>   
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart"   style="height: 100px !important";></canvas>
                </div><!-- end card-body -->
                <div class="card-footer">
                    <ul style="display: flex; list-style-type: none; padding: 0; margin: 0;">
                        <!-- <li style="margin-right: 10px;">
                            <span style="display: inline-block; background-color: red; height: 10px; width: 10px;"></span>
                            lượt trả hàng
                        </li> -->
                        <li style="margin-right: 10px;">
                            <span style="display: inline-block; background-color: green; height: 10px; width: 10px;"></span>
                            Tổng: {{$tong}} sản phẩm
                        </li>
                        <!-- <li>
                            <span style="display: inline-block; background-color: blue; height: 10px; width: 10px;"></span>
                           Tổng: {{$tong}} sản phẩm
                        </li> -->
                    </ul>
                </div>

            </div><!-- end card -->
        </div>
        <div class="col-xl-3">
       
            <table class="table table-striped table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Cửa hàng</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @if(isset($listShop))
                        @foreach($listShop as $shop)
                            <tr>
                                <td>{{$shop->shop_name}}</td>
                                <td>{{number_format($shop->soluong)}} sản phẩm</td> 
                            </tr>
                        @endforeach
                    @endif 
                </tbody>
            </table>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Thống kê doanh thu theo cửa hàng (Tất cả)</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted">Tháng {{ \Carbon\Carbon::now()->format('m') }}<i class="mdi mdi-chevron-down ms-1"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Today</a>
                                <a class="dropdown-item" href="#">Last Week</a>
                                <a class="dropdown-item" href="#">Last Month</a>
                                <a class="dropdown-item" href="#">Current Year</a>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="table-responsive table-card">
                    <table id="shop" class="display" style="width:100%">
                    <thead class="table-light">
                                <tr class="text-muted">
                                    <th scope="col">Tên cửa hàng</th>
                                    <th scope="col" style="width: 20%;">Địa chỉ</th>
                                    <th scope="col">Chủ cửa hàng</th>
                                    <th scope="col" style="width: 16%;">Trạng thái</th>
                                    <th scope="col" style="width: 12%;">So luong</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($listShop as $shop)
                                <tr>
                                    <td>{{$shop->shop_name}}</td>
                                    <td>{{$shop->pick_up_address}} <br> {{$shop->ward}} <br> {{$shop->district}} <br> {{$shop->province}}</td>
                                    <td><img src="{{$shop->user[0]->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                        <a href="#javascript: void(0);" class="text-body fw-medium">{{$shop->user[0]->fullname ?? null}}</a>
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success p-2">Cửa hàng nổi bật</span></td>
                                    <td>
                                        <div class="text-nowrap">{{number_format($shop->soluong)}} sản phẩm</div>
                                    </td>
                                </tr>
                                @endforeach
                                
                            </tbody><!-- end tbody -->
                    </table>
                    <script>
                        new DataTable('#shop');
                    </script>
                    </div><!-- end table responsive -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

    </div><!-- end row -->
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<script>
const xValues1 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];

// Lấy dữ liệu từ PHP cho biểu đồ màu xanh
var red_data = @json($luongtrahangJson ?? []);
var green_data = @json($soluongJson ?? []);
var blue_data = @json($soluongJsond ?? []);

new Chart("myChart", {
  type: "line",
  data: {
    labels: xValues1,
    datasets: [
      { 
        data: red_data,
        borderColor: "red",
        fill: false
      }, 
      { 
        data: green_data,
        borderColor: "green",
        fill: false
      },
      { 
        data: blue_data,
        borderColor: "blue",
        fill: false
      }
    ]
  },
  options: {
    legend: { display: false }
  }
});
</script>

<script>
const xValues2 = @json($listCategoryJson ?? []);
const yValues =  @json($listCategorydoanhthu ?? []);
const barColors = @json($listCategoryColors ?? []);
console.log( barColors);


new Chart("chart", {
  type: "pie",
  data: {
    labels: xValues2,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  }

});
</script>



@endsection






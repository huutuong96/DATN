@extends('index')
@section('title', 'Tổng quan')

@section('main')


<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <div
                                        class="flex-grow-1 overflow-hidden"
                                    >
                                        <p
                                            class="text-uppercase fw-medium text-muted text-truncate mb-0"
                                        >
                                            Cửa hàng cần duyệt
                                        </p>
                                    </div>
                                    <!-- <div
                                        class="flex-shrink-0"
                                    >
                                        <h5
                                            class="text-success fs-14 mb-0"
                                        >
                                            <i
                                                class="ri-arrow-right-up-line fs-13 align-middle"
                                            ></i>
                                            20
                                        </h5>
                                    </div> -->
                                </div>
                                <div
                                    class="d-flex align-items-end justify-content-between mt-4"
                                >
                                    <div>
                                        <h4
                                            class="fs-22 fw-semibold ff-secondary mb-4"
                                        >
                                            <span
                                                class="counter-value"
                                                data-target="{{$checkShop}}"
                                                >0</span
                                            >
                                        </h4>
                                        <a
                                            href="#"
                                            class="text-decoration-underline"
                                            >Cửa hàng cần duyệt</a
                                        >
                                    </div>
                                    <div
                                        class="avatar-sm flex-shrink-0"
                                    >
                                        <span
                                            class="avatar-title bg-success-subtle rounded fs-3"
                                        >
                                            <i
                                                class="ri-store-2-line"
                                            ></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <div
                                        class="flex-grow-1 overflow-hidden"
                                    >
                                        <p
                                            class="text-uppercase fw-medium text-muted text-truncate mb-0"
                                        >
                                            Sản phẩm cần duyệt
                                        </p>
                                    </div>
                                    <!-- <div
                                        class="flex-shrink-0"
                                    >
                                        <h5
                                            class="text-success fs-14 mb-0"
                                        >
                                            <i
                                                class="ri-arrow-right-up-line fs-13 align-middle"
                                            ></i>
                                            20
                                        </h5>
                                    </div> -->
                                </div>
                                <div
                                    class="d-flex align-items-end justify-content-between mt-4"
                                >
                                    <div>
                                        <h4
                                            class="fs-22 fw-semibold ff-secondary mb-4"
                                        >
                                            <span
                                                class="counter-value"
                                                data-target="{{$checkProduct}}"
                                                >0</span
                                            >
                                        </h4>
                                        <a
                                            href="#"
                                            class="text-decoration-underline"
                                            >Sản phẩm cần duyệt</a
                                        >
                                    </div>
                                    <div
                                        class="avatar-sm flex-shrink-0"
                                    >
                                        <span
                                            class="avatar-title bg-info-subtle rounded fs-3"
                                        >
                                            <i
                                                class="ri-archive-fill"
                                            ></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <div
                                        class="flex-grow-1 overflow-hidden"
                                    >
                                        <p
                                            class="text-uppercase fw-medium text-muted text-truncate mb-0"
                                        >
                                        Lợi nhận của sàn trong tháng
                                        </p>
                                    </div>
                                    <!-- <div
                                        class="flex-shrink-0"
                                    >
                                        <h5
                                            class="text-success fs-14 mb-0"
                                        >
                                            <i
                                                class="ri-arrow-right-up-line fs-13 align-middle"
                                            ></i>
                                            20
                                        </h5>
                                    </div> -->
                                </div>
                                <div
                                    class="d-flex align-items-end justify-content-between mt-4"
                                >
                                    <div>
                                        <h4
                                            class="fs-22 fw-semibold ff-secondary mb-4"
                                        >
                                        <span >
                                        {{ number_format($monthlyRevenue)}} vnđ
                                        </span>
                                        </h4>
                                        <a
                                            href="#"
                                            class="text-decoration-underline"
                                            >Doanh thu theo tháng</a
                                        >
                                    </div>
                                    <div
                                        class="avatar-sm flex-shrink-0"
                                    >
                                        <span
                                            class="avatar-title bg-secondary-subtle rounded fs-3"
                                        >
                                            <i
                                                class="ri-refund-2-fill"
                                            ></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center"
                                >
                                    <div
                                        class="flex-grow-1 overflow-hidden"
                                    >
                                        <p
                                            class="text-uppercase fw-medium text-muted text-truncate mb-0"
                                        >
                                            Tổng số cửa hàng đang hoạt động
                                        </p>
                                    </div>
                                    <!-- <div
                                        class="flex-shrink-0"
                                    >
                                        <h5
                                            class="text-success fs-14 mb-0"
                                        >
                                            <i
                                                class="ri-arrow-right-up-line fs-13 align-middle"
                                            ></i>
                                            20
                                        </h5>
                                    </div> -->
                                </div>
                                <div
                                    class="d-flex align-items-end justify-content-between mt-4"
                                >
                                    <div>
                                        <h4
                                            class="fs-22 fw-semibold ff-secondary mb-4"
                                        >
                                            <span
                                                class="counter-value"
                                                data-target="{{$shopAC}}"
                                                >0</span
                                            >
                                        </h4>
                                        <a
                                            href="#"
                                            class="text-decoration-underline"
                                            >feedback</a
                                        >
                                    </div>
                                    <div
                                        class="avatar-sm flex-shrink-0"
                                    >
                                        <span
                                            class="avatar-title bg-danger-subtle rounded fs-3"
                                        >
                                            <i
                                                class=" ri-close-line"
                                            ></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row-->
            </div>
            <!-- end .h-100-->
        </div>
        <!-- end col -->
    </div>
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thống kê tổng quát</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div><!-- end card-body -->
                <div class="card-footer">
                    <ul style="display: flex; list-style-type: none; padding: 0; margin: 0;">
                        <li style="margin-right: 10px;">
                            <span style="display: inline-block; background-color: red; height: 10px; width: 10px;"></span>
                            lượt trả hàng
                        </li>
                        <li style="margin-right: 10px;">
                            <span style="display: inline-block; background-color: green; height: 10px; width: 10px;"></span>
                            Lượt mua sản phẩm
                        </li>
                        <li>
                            <span style="display: inline-block; background-color: blue; height: 10px; width: 10px;"></span>
                            Doanh thu * 1.000.000 vnd
                        </li>
                    </ul>
                </div>

            </div><!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6 ">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thống kê doanh thu</h4>
                </div><!-- end card header -->
                
                <div class="card-body">
                <canvas id="chart"></canvas>
                </div><!-- end card-body -->
                <br>
                <div class="div float-center" style="display: flex; justify-content: center; align-items: center">
                    <b>theo danh mục</b>
                </div>
                <br>
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Hot shop</h4>
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
                        <table class="table table-borderless table-hover table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted">
                                    <th scope="col">Tên shop</th>
                                    <th scope="col" style="width: 20%;">Địa chỉ</th>
                                    <th scope="col">Chủ cửa hàng</th>
                                    <th scope="col" style="width: 16%;">Status</th>
                                    <th scope="col" style="width: 12%;">Doanh thu</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($listShop as $shop)
                                <tr>
                                    <td>{{$shop->shop_name}}</td>
                                    <td>{{$shop->district}}</td>
                                    <td><img src="{{$shop->user[0]->avatar ?? 'assets/images/users/avatar-1.jpg'}}" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                        <a href="#javascript: void(0);" class="text-body fw-medium">{{$shop->user[0]->fullname}}</a>
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success p-2">Hot shop</span></td>
                                    <td>
                                        <div class="text-nowrap">{{number_format($shop->doanhthu)}}vnđ</div>
                                    </td>
                                </tr>
                                @endforeach
                                <!-- <tr>
                                    <td>Raitech Soft</td>
                                    <td>Hà nội</td>
                                    <td><img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                        <a href="#javascript: void(0);" class="text-body fw-medium">Sofia Cunha</a>
                                    </td>
                                    <td><span class="badge bg-warning-subtle text-warning p-2">Intro Call</span></td>
                                    <td>
                                        <div class="text-nowrap">$150K</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>William PVT</td>
                                    <td>Phú quốc</td>
                                    <td><img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                        <a href="#javascript: void(0);" class="text-body fw-medium">Luis Rocha</a>
                                    </td>
                                    <td><span class="badge bg-danger-subtle text-danger p-2">Stuck</span></td>
                                    <td>
                                        <div class="text-nowrap">$78.18K</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Loiusee LLP</td>
                                    <td>Tp. Hồ Chí Minh</td>
                                    <td><img src="assets/images/users/avatar-4.jpg" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                        <a href="#javascript: void(0);" class="text-body fw-medium">Vitoria Rodrigues</a>
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success p-2">Deal Won</span></td>
                                    <td>
                                        <div class="text-nowrap">$180K</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Apple Inc.</td>
                                    <td>Huế</td>
                                    <td><img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle me-2 material-shadow">
                                        <a href="#javascript: void(0);" class="text-body fw-medium">Vitoria Rodrigues</a>
                                    </td>
                                    <td><span class="badge bg-info-subtle text-info p-2">New Lead</span></td>
                                    <td>
                                        <div class="text-nowrap">$78.9K</div>
                                    </td>
                                </tr> -->
                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div><!-- end table responsive -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <!-- <div class="col-xl-5">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">My Tasks</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted"><i class="ri-settings-4-line align-bottom me-1 fs-15"></i>Settings</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Edit</a>
                                <a class="dropdown-item" href="#">Remove</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">

                    <div class="align-items-center p-3 justify-content-between d-flex">
                        <div class="flex-shrink-0">
                            <div class="text-muted"><span class="fw-semibold">4</span> of <span class="fw-semibold">10</span> remaining</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success"><i class="ri-add-line align-middle me-1"></i> Add Task</button>
                    </div>

                    <div data-simplebar style="max-height: 219px;">
                        <ul class="list-group list-group-flush border-dashed px-3">
                            <li class="list-group-item ps-0">
                                <div class="d-flex align-items-start">
                                    <div class="form-check ps-0 flex-sharink-0">
                                        <input type="checkbox" class="form-check-input ms-0" id="task_one">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label mb-0 ps-2" for="task_one">Check yêu cầu từ new shop</label>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <p class="text-muted fs-12 mb-0">15 Sep, 2021</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item ps-0">
                                <div class="d-flex align-items-start">
                                    <div class="form-check ps-0 flex-sharink-0">
                                        <input type="checkbox" class="form-check-input ms-0" id="task_two">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label mb-0 ps-2" for="task_two">Check yêu cầu thêm sản phẩm từ shop</label>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <p class="text-muted fs-12 mb-0">20 Sep, 2021</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item ps-0">
                                <div class="d-flex align-items-start">
                                    <div class="form-check flex-sharink-0 ps-0">
                                        <input type="checkbox" class="form-check-input ms-0" id="task_three">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label mb-0 ps-2" for="task_three">Check yêu cầu cập nhật sản phẩm từ shop</label>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <p class="text-muted fs-12 mb-0">24 Sep, 2021</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item ps-0">
                                <div class="d-flex align-items-start">
                                    <div class="form-check ps-0 flex-sharink-0">
                                        <input type="checkbox" class="form-check-input ms-0" id="task_four">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label mb-0 ps-2" for="task_four">Xem xét các feeback gửi về và giải quyết vấn đề</label>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <p class="text-muted fs-12 mb-0">27 Sep, 2021</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item ps-0">
                                <div class="d-flex align-items-start">
                                    <div class="form-check ps-0 flex-sharink-0">
                                        <input type="checkbox" class="form-check-input ms-0" id="task_five">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label mb-0 ps-2" for="task_five">Xem qua thống kê rồi báo cho quản lý để bàn về hướng phát triển</label>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <p class="text-muted fs-12 mb-0">27 Sep, 2021</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item ps-0">
                                <div class="d-flex align-items-start">
                                    <div class="form-check ps-0 flex-sharink-0">
                                        <input type="checkbox" class="form-check-input ms-0" id="task_six">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label mb-0 ps-2" for="task_six">Nhắn tin kêu thy và hoàng đi nhậu</label>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <p class="text-muted fs-12 mb-0">27 Sep, 2021</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="p-3 pt-2">
                        <a href="javascript:void(0);" class="text-muted text-decoration-underline">Show more...</a>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- end col -->
    </div><!-- end row -->
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<script>
const xValues1 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];

// Lấy dữ liệu từ PHP cho biểu đồ màu xanh
var red_data = @json($luongtrahangJson);
var green_data = @json($luotmuaJson);
var blue_data = @json($doanhthuJson);

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
const xValues2 = @json($listCategoryJson);
const yValues =  @json($listCategorydoanhthu);
const barColors = @json($listCategoryColors);
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






@extends('index')
@section('title', 'Tổng quan')
@section('link')
<!-- ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


@endsection
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
                                            href="{{route('pending_approval_stores',['token' => auth()->user()->refesh_token])}}"
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
                                            href="{{route('product_all',['token' => auth()->user()->refesh_token, 'tab'=>2])}}"
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
                                            >Lợi nhận của sàn trong tháng</a
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
                                            Số cửa hàng đang hoạt động
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
                                            href="{{route('store',['token' => auth()->user()->refesh_token])}}"
                                            class="text-decoration-underline"
                                            >Số cửa hàng đang hoạt động</a
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
                    <h4 class="card-title mb-0">Line & Column Charts</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="chart_line_column_chart" data-colors='["--vz-primary", "--vz-success"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
                <script>
                    // Hàm lấy màu từ thuộc tính data-colors
                    function getChartColorsArray(chartId) {
                        var colors = document.getElementById(chartId).getAttribute("data-colors");
                        if (colors) {
                            colors = JSON.parse(colors).map(function(value) {
                                var newValue = getComputedStyle(document.documentElement).getPropertyValue(value.trim());
                                return newValue ? newValue.trim() : value;
                            });
                        }
                        return colors;
                    }

                    // Lấy màu từ data-colors
                    var chartLineColumnColors = getChartColorsArray("chart_line_column_chart");

                    // Cấu hình biểu đồ
                    var options = {
                        series: [
                            {
                                name: "Website Blog",
                                type: "column",
                                data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160],
                            },
                            {
                                name: "Social Media",
                                type: "line",
                                data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16],
                            },
                        ],
                        chart: {
                            height: 350,
                            type: "line",
                            toolbar: { show: false },
                        },
                        stroke: {
                            width: [0, 4],
                        },
                        title: {
                            text: "Traffic Sources",
                            style: { fontWeight: 500 },
                        },
                        dataLabels: {
                            enabled: true,
                            enabledOnSeries: [1],
                        },
                        labels: [
                            "01 Jan 2001",
                            "02 Jan 2001",
                            "03 Jan 2001",
                            "04 Jan 2001",
                            "05 Jan 2001",
                            "06 Jan 2001",
                            "07 Jan 2001",
                            "08 Jan 2001",
                            "09 Jan 2001",
                            "10 Jan 2001",
                            "11 Jan 2001",
                            "12 Jan 2001",
                        ],
                        xaxis: {
                            type: "datetime",
                        },
                        yaxis: [
                            {
                                title: {
                                    text: "Website Blog",
                                    style: { fontWeight: 500 },
                                },
                            },
                            {
                                opposite: true,
                                title: {
                                    text: "Social Media",
                                    style: { fontWeight: 500 },
                                },
                            },
                        ],
                        colors: chartLineColumnColors,
                    };

                    // Khởi tạo biểu đồ
                    var chart = new ApexCharts(document.querySelector("#chart_line_column_chart"), options);
                    chart.render();
                </script>

                <!-- <div class="card-header">
                    <h4 class="card-title mb-0">Thống kê tổng quát</h4>
                </div> 
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
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
                </div> -->

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
   
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<!-- 
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
</script> -->

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






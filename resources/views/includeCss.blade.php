<!DOCTYPE html>
<html
    lang="en"
    data-layout="vertical"
    data-topbar="light"
    data-sidebar="dark"
    data-sidebar-size="lg"
    data-sidebar-image="none"
    data-preloader="disable"
    data-theme="default"
    data-theme-colors="default">
<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:44:28 GMT -->

<head>
    <meta charset="utf-8" />
    <title>Admin VNSHOP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
   

    <!-- jsvectormap css -->
    <link href="{{env('URL_CSS_PRODUCTION')}}assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />
    <!--Swiper slider css-->
    <link href="{{env('URL_CSS_PRODUCTION')}}assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{env('URL_CSS_PRODUCTION')}}assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{env('URL_CSS_PRODUCTION')}}assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{env('URL_CSS_PRODUCTION')}}assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{env('URL_CSS_PRODUCTION')}}assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    @yield('link')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
       
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                @yield('embed')
                
            </div>
            <!-- End Page-content -->

         
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!--start back-to-top-->
    <button
        onclick="topFunction()"
        class="btn btn-danger btn-icon"
        id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <!-- <div id="preloader">
        <div id="status">
            <div
                class="spinner-border text-primary avatar-sm"
                role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div> -->
    <!-- JAVASCRIPT -->
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/node-waves/waves.min.js"></script>
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/feather-icons/feather.min.js"></script>
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/js/plugins.js"></script>

    <!-- apexcharts -->
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="{{env('URL_CSS_PRODUCTION')}}assets/js/pages/dashboard-ecommerce.init.js"></script>

    <script src="{{env('URL_CSS_PRODUCTION')}}assets/js/app.js"></script>
</body>

<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:45:33 GMT -->

</html>
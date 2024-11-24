<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/dashboard-nft.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:07 GMT -->
<head>

    <meta charset="utf-8" />
    <title>NFT Dashboard | Velzon - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" style="width: auto; margin: 0; padding: 0;">

            <div class="page-content" style="width: auto; margin: 0; padding: 0;">
                <div class="container-fluid">
                    <div class="row dash-nft">
                        <div class="col-xxl-6">
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <div class="card overflow-hidden">
                                        <div class="card-body bg-marketplace d-flex">
                                            <div class="flex-grow-1">
                                                <h4 class="fs-18 lh-base mb-0">VÍ CỦA CỬA HÀNG:  <br> <span class="text-success">{{$shop->shop_name ?? null}}</span> </h4>
                                                <p class="mb-0 mt-2 pt-1 text-muted">Rút tiền từ ví về tài khoản ngân hàng của bạn</p>
                                                <div class="d-flex gap-3 mt-4">
                                                    @if($shop->account_number == null || $shop->bank_name == null || $shop->owner_bank == null )
                                                        <button class="btn btn-success" disabled>RÚT TIỀN</button>
                                                    @else
                                                        <a href="#!"  class="btn btn-success"  data-bs-toggle="modal" data-bs-target=".exampleModalFullscreen1">RÚT TIỀN</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <img src="assets/images/bg-d.png" alt="" class="img-fluid" />
                                        </div>
                                    </div>
                                </div><!--end col-->
                            
                                <div class="col-xl-4 col-md-3">
                                    <div class="card card-height-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-info-subtle rounded fs-3">
                                                        <i class="bx bx-wallet text-info"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 ps-3">
                                                    <h5 class="fs-18 lh-base mb-0 ">VÍ VNSHOP</h5>
                                                </div>
                                            </div>
                                            <div class="mt-4 pt-1">
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" >{{number_format($shop->wallet) ?? 0}}đ</span> </h4>
                                                @if($shop->account_number == null || $shop->bank_name == null || $shop->owner_bank == null )
                                                    <p class="mt-4 mb-0 text-muted"><span class="badge bg-success-subtle text-danger mb-0" >CHƯA THIẾT LẬP TÀI KHOẢN NGÂN HÀNG</span></p><br>
                                                    <a href="#!" class="btn btn-success" data-bs-toggle="modal" data-bs-target=".exampleModalFullscreen">TÀI KHOẢN NGÂN HÀNG</a>
                                                @else
                                                    <p class="mt-4 mb-0 text-muted"><span class="badge bg-success-subtle text-success mb-0" style="font-size: 13px;">CÓ THỂ RÚT TIỀN</span></p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row-->

                            
                        </div><!--end col-->

                        
                    </div> 
                    <!--end row-->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
<!-- Full screen modal content -->
<div class="modal fade exampleModalFullscreen" tabindex="-1" aria-labelledby="exampleModalFullscreenLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">THIẾT LẬP TÀI KHOẢN NGÂN HÀNG CHO CỬA HÀNG</h4>
                                </div><!-- end card header -->
                                <div class="card-body">
                                    <form action="{{route('updateBank', ['shop_id' => $shop->id])}}" class="form-steps" method="post">
                                        @csrf
                                        <div class="text-center pt-3 pb-4 mb-1 d-flex justify-content-center">
                                            <img src="{{$config->logo_header}}" class="card-logo card-logo-dark" alt="logo dark" height="17">
                                        </div>
                                        <div class="step-arrow-nav mb-4">

                                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link done" id="steparrow-gen-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-gen-info" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="false" data-position="0" tabindex="-1">Tài khoản ngân hàng</button>
                                                </li>
                                                <!-- <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="steparrow-description-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-description-info" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="true" data-position="1">Description</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill" data-bs-target="#pills-experience" type="button" role="tab" aria-controls="pills-experience" aria-selected="false" data-position="2" tabindex="-1">Finish</button>
                                                </li> -->
                                            </ul>
                                        </div>

                                        <div class="tab-content">
                                          
                                                <div class="tab-pane fade show active" id="steparrow-gen-info" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                                                    <div>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label" for="steparrow-gen-info-email-input">Số Tài khoản</label>
                                                                    <input name="account_number" type="text" class="form-control" id="steparrow-gen-info-email-input" placeholder="ex: 0987654321" required="">
                                                                    <div class="invalid-feedback">Vui lòng nhập số tài khoản</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label" for="steparrow-gen-info-username-input">Nhập Tên Ngân hàng</label>
                                                                    <input name="bank_name" type="text" class="form-control" id="steparrow-gen-info-username-input" placeholder="ex: VCB" required="">
                                                                    <div class="invalid-feedback">Vui lòng nhập tên ngân hàng</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="form-label" for="steparrow-gen-info-confirm-password-input">Nhập tên chủ sở hữu</label>
                                                            <input name="owner_bank" type="text" class="form-control" id="steparrow-gen-info-confirm-password-input" placeholder="ex: Nguyễn Văn A" required="">
                                                            <div class="invalid-feedback">Vui lòng nhập tên chủ sở hữu</div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="submit" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="steparrow-description-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Thêm Số Tài Khoản</button>
                                                    </div>
                                                </div>
                                                <!-- end tab pane -->

                                                <!-- <div class="tab-pane fade show active" id="steparrow-description-info" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                                                    <div>
                                                        <div class="mb-3">
                                                            <label for="formFile" class="form-label">Upload Image</label>
                                                            <input class="form-control" type="file" id="formFile">
                                                        </div>
                                                        <div>
                                                            <label class="form-label" for="des-info-description-input">Description</label>
                                                            <textarea class="form-control" placeholder="Enter Description" id="des-info-description-input" rows="3" required=""></textarea>
                                                            <div class="invalid-feedback">Please enter a description</div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" data-previous="steparrow-gen-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Back to General</button>
                                                        <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-experience-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
                                                    </div>
                                                </div> -->
                                                <!-- end tab pane -->

                                                <!-- <div class="tab-pane fade" id="pills-experience" role="tabpanel" aria-labelledby="pills-experience-tab">
                                                    <div class="text-center">

                                                        <div class="avatar-md mt-5 mb-4 mx-auto">
                                                            <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                                                <i class="ri-checkbox-circle-fill"></i>
                                                            </div>
                                                        </div>
                                                        <h5>Well Done !</h5>
                                                        <p class="text-muted">You have Successfully Signed Up</p>
                                                    </div>
                                                </div> -->
                                                <!-- end tab pane -->
                                           
                                        </div>
                                        <!-- end tab content -->
                                    </form>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade exampleModalFullscreen1" tabindex="-1" aria-labelledby="exampleModalFullscreenLabel1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">NHẬP SỐ TIỀN RÚT</h4>
                                </div><!-- end card header -->
                                <div class="card-body">
                                    <form action="{{route('shop_request_get_cash')}}" class="form-steps" method="post">
                                        @csrf
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="text-center pt-3 pb-4 mb-1 d-flex justify-content-center">
                                            <img src="{{$config->logo_header}}" class="card-logo card-logo-dark" alt="logo dark" height="17">
                                        </div>
                                        <div class="step-arrow-nav mb-4">

                                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link done" id="steparrow-gen-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-gen-info" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="false" data-position="0" tabindex="-1">RÚT TIỀN VỀ TÀI KHOẢN</button>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="tab-content">
                                          
                                                <div class="tab-pane fade show active" id="steparrow-gen-info" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                                                    <div>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label class="form-label" for="steparrow-gen-info-email-input">SỐ TIỀN</label>
                                                                    <input name="get_cash" type="text" class="form-control" id="steparrow-gen-info-email-input" placeholder="ex: 0987654321" required="">
                                                                    <input type="hidden" name="user" value="{{$user->id}}">
                                                                    <input type="hidden" name="shop_id" value="{{$shop->id}}">
                                                                    <div class="invalid-feedback">Vui lòng nhập số tiền</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="submit" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="steparrow-description-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>RÚT TIỀN</button>
                                                    </div>
                                                </div>
                                                <!-- end tab pane -->
                                           
                                        </div>
                                        <!-- end tab content -->
                                    </form>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
   

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!-- Countdown js -->
    <script src="assets/js/pages/coming-soon.init.js"></script>

    <!-- Marketplace init -->
    <script src="assets/js/pages/dashboard-nft.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/dashboard-nft.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:13 GMT -->
</html>
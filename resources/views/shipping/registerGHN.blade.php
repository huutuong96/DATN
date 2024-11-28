@extends('includeCss')
@section('title', 'Đăng ký đơn vị vận chuyển')

@section('embed')

<div class="container-fluid">
                 
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Đăng ký đơn vị vận chuyển</h4>
                                </div><!-- end card header -->
                                <div class="card-body">
                                    <form action="{{route('register_shipping')}}" class="form-steps" autocomplete="off" method="post">
                                        @csrf 
                                        <div class="text-center pt-3 pb-4 mb-1 d-flex justify-content-center">
                                            <img src="assets/images/logo-dark.png" class="card-logo card-logo-dark" alt="logo dark" height="17">
                                            <img src="assets/images/logo-light.png" class="card-logo card-logo-light" alt="logo light" height="17">
                                        </div>
                                        <div class="step-arrow-nav mb-4">

                                            <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="steparrow-gen-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-gen-info" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="true" data-position="0">General</button>
                                                </li>
                                              
                                            </ul>
                                        </div>

                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="steparrow-gen-info" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                                                <div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="steparrow-gen-info-email-input">Email</label>
                                                                <input name="email" type="email" class="form-control" id="steparrow-gen-info-email-input" placeholder="Nhập Email" required="">
                                                                <div class="invalid-feedback">Please enter an email address</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="steparrow-gen-info-username-input">Số điện thoại</label>
                                                                <input name="phone" type="text" class="form-control" id="steparrow-gen-info-username-input" placeholder="Nhập số điện thoại" required="">
                                                                <div class="invalid-feedback">Please enter a user name</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="steparrow-gen-info-email-input">Họ và tên</label>
                                                                <input name="fullname" type="text" class="form-control" id="steparrow-gen-info-email-input" placeholder="Nhập họ tên" required="">
                                                                <div class="invalid-feedback">Please enter an email address</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                                                                    </div>
                                                <div class="d-flex align-items-start gap-3 mt-4">
                                                    <button type="submit" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="steparrow-description-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Đăng ký</button>
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
                        <!-- end col -->
                    </div>


</div>
@endsection
@extends('index')
@section('title', 'Thông tin tài khoản')

@section('main')


<div class="container-fluid">

<!-- <div class="position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg profile-setting-img">
        <img src="assets/images/profile-bg.jpg" class="profile-wid-img" alt="">
        <div class="overlay-content">
            <div class="text-end p-3">
                <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                    <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input">
                    <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                        <i class="ri-image-edit-line align-bottom me-1"></i> Thay đổi ảnh bìa
                    </label>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="row mt-5">
    <div class="col-xxl-3">
        <div class="card mt-n5">
            <div class="card-body p-4">
                <div class="text-center">
                    <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                            <img src="{{ auth()->user()->avatar }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image material-shadow" alt="user-profile-image">
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                            <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body material-shadow">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-1">{{ auth()->user()->fullname }}</h5>
                    <p class="text-muted mb-0">{{ auth()->user()->role->title }}</p>
                </div>
            </div>
        </div>
        <!--end card-->
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">Liên kết</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="javascript:void(0);" class="badge bg-light text-primary fs-12"><i class="ri-add-fill align-bottom me-1"></i> Thêm</a>
                    </div>
                </div>
                <div class="mb-3 d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle fs-16 bg-body text-body material-shadow">
                            <i class="ri-mail-fill"></i>
                        </span>
                    </div>
                    <input type="email" class="form-control" id="gitUsername" placeholder="Username" value="{{ auth()->user()->email }}">
                </div>
                <div class="mb-3 d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle fs-16 bg-primary material-shadow">
                            <i class="ri-global-fill"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="websiteInput" placeholder="www.example.com" value="www.vnshop.top">
                </div>
                <div class="mb-3 d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle fs-16 bg-success material-shadow">
                            <i class="ri-dribbble-fill"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="dribbleName" placeholder="Username" value="@dave_adame">
                </div>
                <div class="d-flex">
                    <div class="avatar-xs d-block flex-shrink-0 me-3">
                        <span class="avatar-title rounded-circle fs-16 bg-danger material-shadow">
                            <i class="ri-pinterest-fill"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="pinterestName" placeholder="Username" value="Advance Dave">
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
    <div class="col-xxl-9">
        <div class="card mt-xxl-n5">
            <div class="card-header">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                            <i class="fas fa-home"></i> Chi tiết cá nhân
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                            <i class="far fa-user"></i> Đổi mật khẩu
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content">
                    <div class="tab-pane active" id="personalDetails" role="tabpanel">
                        <form action="{{ route('update_profile',['token' => auth()->user()->refesh_token]) }}" method="POST">
                            @csrf
                        <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="firstnameInput" class="form-label">Tên</label>
                                        <input name="fullname" type="text" class="form-control" id="firstnameInput" placeholder="Nhập tên của bạn" value="{{ auth()->user()->fullname }}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="lastnameInput" class="form-label">Ngày sinh</label>
                                        <input name="datebirth" type="date" class="form-control" id="lastnameInput" placeholder="Ngày sinh" value="{{ auth()->user()->datebirth }}">                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="phonenumberInput" class="form-label">Số điện thoại</label>
                                        <input name="phone" type="text" class="form-control" id="phonenumberInput" placeholder="Nhập số điện thoại của bạn" value="{{ auth()->user()->phone }}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="emailInput" class="form-label">Địa chỉ Email</label>
                                        <input name="email" type="email" class="form-control" id="emailInput" placeholder="Nhập email của bạn" value="{{ auth()->user()->email }}">
                                    </div>
                                </div>
                                <!--end col-->
                               
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="cityInput" class="form-label">Thành phố / Tỉnh</label>
                                        <input type="text" class="form-control" id="cityInput" placeholder="Thành phố / Tỉnh" value="{{ auth()->user()->address()->where('default', 1)->first()->province ?? null }}" />
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="countryInput" class="form-label">Huyện</label>
                                        <input type="text" class="form-control" id="countryInput" placeholder="Huyện" value="{{ auth()->user()->address()->where('default', 1)->first()->district ?? null }}" />
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="zipcodeInput" class="form-label">Xã Phường</label>
                                        <input type="text" class="form-control" minlength="5" maxlength="6" id="zipcodeInput" placeholder="Xã / Phường" value="{{ auth()->user()->address()->where('default', 1)->first()->ward ?? null }}">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="mb-3 pb-2">
                                        <label for="exampleFormControlTextarea" class="form-label">Giới thiệu</label>
                                        <textarea name="description" class="form-control" id="exampleFormControlTextarea" placeholder="Chưa có giới thiệu" rows="3">{{ auth()->user()->description ?? null }}</textarea>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                    <!--end tab-pane-->
                    <div class="tab-pane" id="changePassword" role="tabpanel">
                        <form action="{{route('change_password', ['token' => auth()->user()->refesh_token])}}" method="POST">
                            <div class="row g-2">
                                <div class="col-lg-4">
                                    <div>
                                        <label for="oldpasswordInput" class="form-label">Mật khẩu cũ*</label>
                                        <input name="password" type="password" class="form-control" id="oldpasswordInput" placeholder="Nhập mật khẩu cũ">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                          <label for="newpasswordInput" class="form-label">Mật khẩu mới*</label>
                                        <input name="new_password" type="password" class="form-control" id="newpasswordInput" placeholder="Nhập mật khẩu mới">
                                     </div>
                                  </div>
                                  <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    </div>
                                </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

</div>
          

@endsection






@extends('index')
@section('title', 'List Store')

@section('main')
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Sản phẩm</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Cửa hàng</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Tài khoản</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="disabled-tab" data-bs-toggle="tab" data-bs-target="#disabled-tab-pane" type="button" role="tab" aria-controls="disabled-tab-pane" aria-selected="false" disabled>Bài viết</button>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade {{$tab == 1 ? 'show active' : ''}}" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
        1
    </div>
    <div class="tab-pane fade {{$tab == 2 ? 'show active' : ''}}" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
        2
    </div>
    <div class="tab-pane fade {{$tab == 3 ? 'show active' : ''}}" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
        3
    </div>
    <div class="tab-pane fade {{$tab == 4 ? 'show active' : ''}}" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">
        4
    </div>
</div>
@endsection

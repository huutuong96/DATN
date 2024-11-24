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
    <link rel="shortcut icon" href="{{ $config->icon }}">

    <!-- jsvectormap css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

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

    @yield('link')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ route('dashboard', ['token' => auth()->user()->refesh_token]) }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img
                                        src="{{ $config->logo_admin }}"
                                        alt=""
                                        height="50" />
                                </span>
                                <span class="logo-lg">
                                    <img
                                        src="{{ $config->logo_admin }}"
                                        alt=""
                                        height="50" />
                                </span>
                            </a>

                            <a href="{{ route('dashboard', ['token' => auth()->user()->refesh_token]) }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img
                                        src="{{ $config->logo_admin }}"
                                        alt=""
                                        height="50" />
                                </span>
                                <span class="logo-lg">
                                    <img
                                        src="{{ $config->logo_admin }}"
                                        alt=""
                                        height="50" />
                                </span>
                            </a>
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>
                    <form class="col-xl-6 p-3" action="{{ route('admin_search',[
                                                                'token' => auth()->user()->refesh_token,
                                                                ]) }}" method="POST" style="display:inline;">
                        @csrf
                        <!-- <form class="col-xl-6 p-3"> -->
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input name='search' type="text" class="form-control" placeholder="Tìm kiếm ..." aria-label="Recipient's username">
                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex align-items-center">
                        <div
                            class="dropdown d-md-none topbar-head-dropdown header-item">
                            <button
                                type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                id="page-header-search-dropdown"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                        </div>
                        <div class="dropdown topbar-head-dropdown ms-1 header-item">
                            <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-category-alt fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end" style="">
                                <div class="p-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0 fw-semibold fs-15"> Web Apps </h6>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('list_app', ['token' => auth()->user()->refesh_token]) }}" class="btn btn-sm btn-soft-info"> View All Apps
                                                <i class="ri-arrow-right-s-line align-middle"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div class="row g-0">
                                        @foreach ($apps as $index => $app)
                                            @if ($index % 3 == 0 && $index != 0)
                                                </div><div class="row g-0">
                                            @endif
                                            <div class="col">
                                                <a class="dropdown-icon-item" href="{{$app->url}}" target="_blank">
                                                    <img src="{{$app->icon}}" alt="app">
                                                    <span>{{$app->name}}</span>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="dropdown topbar-head-dropdown ms-1 header-item"
                            id="notificationDropdown">
                            <button
                                type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                id="page-header-notifications-dropdown"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-bell fs-22"></i>
                                <span
                                    class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{ count($notifyMain)  }}<span class="visually-hidden">unread messages</span></span>
                            </button>
                            <div
                                class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div
                                    class="dropdown-head bg-primary bg-pattern rounded-top">
                                    <div class="p-3">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6
                                                    class="m-0 fs-16 fw-semibold text-white">
                                                    Thông báo
                                                </h6>
                                            </div>
                                            <div
                                                class="col-auto dropdown-tabs">
                                                <span
                                                    class="badge bg-light text-body fs-13">
                                                    {{ count($notifyMain) }} New</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-2 pt-2">
                                        <ul
                                            class="nav nav-tabs dropdown-tabs nav-tabs-custom"
                                            data-dropdown-tabs="true"
                                            id="notificationItemsTab"
                                            role="tablist">
                                            <li
                                                class="nav-item waves-effect waves-light">
                                                <a
                                                    class="nav-link active"
                                                    data-bs-toggle="tab"
                                                    href="#all-noti-tab"
                                                    role="tab"
                                                    aria-selected="true">
                                                    All ({{ count($notifyMain) }})
                                                </a>
                                            </li>
                                            <li
                                                class="nav-item waves-effect waves-light">
                                                <a
                                                    class="nav-link"
                                                    data-bs-toggle="tab"
                                                    href="#messages-tab"
                                                    role="tab"
                                                    aria-selected="false">
                                                    Messages
                                                </a>
                                            </li>
                                            <li
                                                class="nav-item waves-effect waves-light">
                                                <a
                                                    class="nav-link"
                                                    data-bs-toggle="tab"
                                                    href="#alerts-tab"
                                                    role="tab"
                                                    aria-selected="false">
                                                    Alerts
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div
                                    class="tab-content position-relative"
                                    id="notificationItemsTabContent">
                                    <div
                                        class="tab-pane fade show active py-2 ps-2"
                                        id="all-noti-tab"
                                        role="tabpanel">
                                        <div
                                            data-simplebar
                                            style="max-height: 300px"
                                            class="pe-2">

                                            @if ($notifyMain->isNotEmpty())
                                                @foreach ($notifyMain as $notify)
                                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                                        <div class="d-flex">
                                                            <img
                                                                src="{{ $notify->image ?? '' }}"
                                                                class="me-3 rounded-circle avatar-sm flex-shrink-0"
                                                                alt="user-pic" />
                                                            <div
                                                                class="flex-grow-1">
                                                                <a
                                                                    href="#!"
                                                                    class="stretched-link">
                                                                    <h6
                                                                        class="mt-0 mb-1 fs-13 fw-semibold">
                                                                        {{ $notify->title ?? '' }}
                                                                    </h6>
                                                                </a>
                                                                <div
                                                                    class="fs-13 text-muted">
                                                                    <p class="mb-1">
                                                                        {{ $notify->description  ?? '' }}
                                                                    </p>
                                                                </div>
                                                                <p
                                                                    class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                    <span><i
                                                                            class="mdi mdi-clock-outline"></i>
                                                                        {{ $notify->created_at->diffForHumans() ?? '' }}</span>
                                                                        </span>
                                                                </p>
                                                            </div>
                                                            <div class="px-2 fs-15">
                                                                <div class="form-check notification-check">
                                                                    <input
                                                                        class="form-check-input"
                                                                        type="checkbox"
                                                                        value="{{ $notify->id }}"
                                                                        id="all-notification-check{{ $notify->id }}"
                                                                        onchange="saveNotificationToSession(this)" />
                                                                    <label
                                                                        class="form-check-label"
                                                                        for="all-notification-check{{ $notify->id }}"></label>
                                                                </div>
                                                            </div>

                                                            <script>
                                                                function saveNotificationToSession(checkbox) {
                                                                    let notifications = JSON.parse(sessionStorage.getItem('notifications')) || [];
                                                                    if (checkbox.checked) {
                                                                        notifications.push(checkbox.value);
                                                                    } else {
                                                                        notifications = notifications.filter(id => id !== checkbox.value);
                                                                    }
                                                                    sessionStorage.setItem('notifications', JSON.stringify(notifications));
                                                                }
                                                            </script>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div
                                                class="my-3 text-center view-all">
                                                <a href="{{ route('list_notification', ['token' => auth()->user()->refesh_token]) }}" class="btn btn-soft-success waves-effect waves-light">
                                                    Xem tất cả thông báo
                                                    <i
                                                        class="ri-arrow-right-line align-middle"></i>
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div
                                        class="tab-pane fade py-2 ps-2"
                                        id="messages-tab"
                                        role="tabpanel"
                                        aria-labelledby="messages-tab">
                                        <div
                                            data-simplebar
                                            style="max-height: 300px"
                                            class="pe-2">
                                            <div
                                                class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img
                                                        src="assets/images/users/avatar-3.jpg"
                                                        class="me-3 rounded-circle avatar-xs"
                                                        alt="user-pic" />
                                                    <div
                                                        class="flex-grow-1">
                                                        <a
                                                            href="#!"
                                                            class="stretched-link">
                                                            <h6
                                                                class="mt-0 mb-1 fs-13 fw-semibold">
                                                                James Lemire
                                                            </h6>
                                                        </a>
                                                        <div
                                                            class="fs-13 text-muted">
                                                            <p class="mb-1">
                                                                We talked
                                                                about a
                                                                project on
                                                                linkedin.
                                                            </p>
                                                        </div>
                                                        <p
                                                            class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i
                                                                    class="mdi mdi-clock-outline"></i>
                                                                30 min
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div
                                                            class="form-check notification-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                value=""
                                                                id="messages-notification-check01" />
                                                            <label
                                                                class="form-check-label"
                                                                for="messages-notification-check01"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img
                                                        src="assets/images/users/avatar-2.jpg"
                                                        class="me-3 rounded-circle avatar-xs"
                                                        alt="user-pic" />
                                                    <div
                                                        class="flex-grow-1">
                                                        <a
                                                            href="#!"
                                                            class="stretched-link">
                                                            <h6
                                                                class="mt-0 mb-1 fs-13 fw-semibold">
                                                                Angela
                                                                Bernier
                                                            </h6>
                                                        </a>
                                                        <div
                                                            class="fs-13 text-muted">
                                                            <p class="mb-1">
                                                                Answered to
                                                                your comment
                                                                on the cash
                                                                flow
                                                                forecast's
                                                                graph 🔔.
                                                            </p>
                                                        </div>
                                                        <p
                                                            class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i
                                                                    class="mdi mdi-clock-outline"></i>
                                                                2 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div
                                                            class="form-check notification-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                value=""
                                                                id="messages-notification-check02" />
                                                            <label
                                                                class="form-check-label"
                                                                for="messages-notification-check02"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img
                                                        src="assets/images/users/avatar-6.jpg"
                                                        class="me-3 rounded-circle avatar-xs"
                                                        alt="user-pic" />
                                                    <div
                                                        class="flex-grow-1">
                                                        <a
                                                            href="#!"
                                                            class="stretched-link">
                                                            <h6
                                                                class="mt-0 mb-1 fs-13 fw-semibold">
                                                                Kenneth
                                                                Brown
                                                            </h6>
                                                        </a>
                                                        <div
                                                            class="fs-13 text-muted">
                                                            <p class="mb-1">
                                                                Mentionned
                                                                you in his
                                                                comment on
                                                                📃 invoice
                                                                #12501.
                                                            </p>
                                                        </div>
                                                        <p
                                                            class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i
                                                                    class="mdi mdi-clock-outline"></i>
                                                                10 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div
                                                            class="form-check notification-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                value=""
                                                                id="messages-notification-check03" />
                                                            <label
                                                                class="form-check-label"
                                                                for="messages-notification-check03"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img
                                                        src="assets/images/users/avatar-8.jpg"
                                                        class="me-3 rounded-circle avatar-xs"
                                                        alt="user-pic" />
                                                    <div
                                                        class="flex-grow-1">
                                                        <a
                                                            href="#!"
                                                            class="stretched-link">
                                                            <h6
                                                                class="mt-0 mb-1 fs-13 fw-semibold">
                                                                Maureen
                                                                Gibson
                                                            </h6>
                                                        </a>
                                                        <div
                                                            class="fs-13 text-muted">
                                                            <p class="mb-1">
                                                                We talked
                                                                about a
                                                                project on
                                                                linkedin.
                                                            </p>
                                                        </div>
                                                        <p
                                                            class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i
                                                                    class="mdi mdi-clock-outline"></i>
                                                                3 days
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div
                                                            class="form-check notification-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                value=""
                                                                id="messages-notification-check04" />
                                                            <label
                                                                class="form-check-label"
                                                                for="messages-notification-check04"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="my-3 text-center view-all">
                                                <button
                                                    type="button"
                                                    class="btn btn-soft-success waves-effect waves-light">
                                                    View All Messages
                                                    <i
                                                        class="ri-arrow-right-line align-middle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade p-4"
                                        id="alerts-tab"
                                        role="tabpanel"
                                        aria-labelledby="alerts-tab"></div>

                                    <div
                                        class="notification-actions"
                                        id="notification-actions">
                                        <div
                                            class="d-flex text-muted justify-content-center">
                                            Đã chọn 
                                            <div
                                                id="select-content"
                                                class="text-body fw-semibold px-1">
                                                {{ $notifyMain->count() ?? 0 }}
                                            </div>
                                            <!-- Result -->
                                            <button
                                                type="button"
                                                class="btn btn-link link-danger p-0 ms-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#removeNotificationModal">
                                                Xóa thông báo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="dropdown ms-sm-3 header-item topbar-user">
                            <button
                                type="button"
                                class="btn material-shadow-none"
                                id="page-header-user-dropdown"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img
                                        class="rounded-circle header-profile-user"
                                        src="{{ auth()->user()->avatar ?? 'https://th.bing.com/th/id/OIP.audMX4ZGbvT2_GJTx2c4GgHaHw?w=188&h=196&c=7&r=0&o=5&dpr=1.3&pid=1.7'}}"
                                        alt="Header Avatar" />
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ auth()->user()->fullname }}</span>
                                        <span
                                            class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ auth()->user()->role->title }}</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">
                                    VNSHOP Admin
                                </h6>
                                <a
                                    class="dropdown-item"
                                    href="{{ route('admin_profile', ['token' => auth()->user()->refesh_token]) }}"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Thông tin</span></a>
                                <a
                                    class="dropdown-item"
                                    href="{{ route('login') }}"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                                    <span
                                        class="align-middle"
                                        data-key="t-logout">Đăng xuất</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- removeNotificationModal -->
        <div
            id="removeNotificationModal"
            class="modal fade zoomIn"
            tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon
                                src="https://cdn.lordicon.com/gsqxdxog.json"
                                trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548"
                                style="width: 100px; height: 100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Bạn có chắc ?</h4>
                                <p class="text-muted mx-4 mb-0">
                                   Xóa những thông báo này?
                                </p>
                            </div>
                        </div>
                        <div
                            class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button
                                type="button"
                                class="btn w-sm btn-light"
                                data-bs-dismiss="modal">
                                Đóng
                            </button>
                            <a href="#" 
                                type="button" 
                                class="btn w-sm btn-danger" 
                                id="delete-notification" 
                                onclick="updateDeleteLink()">
                                Đồng ý
                                </a>

                                <script>
                                    function updateDeleteLink() {
                                        const selectedIds = getSelectedNotificationIds();
                                        const token = '{{ auth()->user()->refesh_token }}';
                                        const baseUrl = '{{ route('delete_notify', ['token' => 'TOKEN_PLACEHOLDER']) }}';
                                        const url = baseUrl.replace('TOKEN_PLACEHOLDER', token) + '&ids=' + selectedIds.join(',');
                                        document.getElementById('delete-notification').href = url;
                                    }

                                    function getSelectedNotificationIds() {
                                        let notifications = JSON.parse(sessionStorage.getItem('notifications')) || [];
                                        return notifications;
                                    }
                                </script>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('dashboard', ['token' => auth()->user()->refesh_token]) }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img
                            src="{{ $config->logo_admin }}"
                            alt=""
                            height="50" />
                    </span>
                    <span class="logo-lg">
                        <img
                            src="{{ $config->logo_admin }}"
                            alt=""
                            height="50" />
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('dashboard', ['token' => auth()->user()->refesh_token]) }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img
                            src="{{ $config->logo_admin }}"
                            alt=""
                            height="50" />
                    </span>
                    <span class="logo-lg">
                        <img
                            src="{{ $config->logo_admin }}"
                            alt=""
                            height="50" />
                    </span>
                </a>
                <button
                    type="button"
                    class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div class="dropdown sidebar-user m-1 rounded">
                <button
                    type="button"
                    class="btn material-shadow-none"
                    id="page-header-user-dropdown"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <span class="d-flex align-items-center gap-2">
                        <img
                            class="rounded header-profile-user"
                            src="assets/images/users/avatar-1.jpg"
                            alt="Header Avatar" />
                        <span class="text-start">
                            <span
                                class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
                            <span
                                class="d-block fs-14 sidebar-user-name-sub-text"><i
                                    class="ri ri-circle-fill fs-10 text-success align-baseline"></i>
                                <span class="align-middle">Online</span></span>
                        </span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <h6 class="dropdown-header">Welcome Anna!</h6>
                    <a class="dropdown-item" href="pages-profile.html"><i
                            class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Profile</span></a>
                    <a class="dropdown-item" href="apps-chat.html"><i
                            class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Messages</span></a>
                    <a class="dropdown-item" href="apps-tasks-kanban.html"><i
                            class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Taskboard</span></a>
                    <a class="dropdown-item" href="pages-faqs.html"><i
                            class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Help</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="pages-profile.html"><i
                            class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Balance : <b>$5971.67</b></span></a>
                    <a
                        class="dropdown-item"
                        href="pages-profile-settings.html"><span
                            class="badge bg-success-subtle text-success mt-1 float-end">New</span><i
                            class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Settings</span></a>
                    <a
                        class="dropdown-item"
                        href="auth-lockscreen-basic.html"><i
                            class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">Lock screen</span></a>
                    <a class="dropdown-item" href="auth-logout-basic.html"><i
                            class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle" data-key="t-logout">Logout</span></a>
                </div>
            </div>
            <div id="scrollbar">
                <div class="container-fluid">
                    <div id="two-column-menu"></div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="{{ route('dashboard', ['token' => auth()->user()->refesh_token]) }}">
                                <span data-key="t-dashboards"><b>MENU</b></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#quanlydoanhthu"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class=" ri-currency-line"></i>
                                <span data-key="t-dashboards">Trang quản lý</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="quanlydoanhthu">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('statist_revenue', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Quản lý tổng quát và doanh thu
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                             href="{{ route('statist.quantity_sold', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-ecommerce">
                                            Quản lý tổng quát theo số lượng sản phẩm bán ra
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                             href="{{ route('statist.sales', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-ecommerce">
                                            Quản lý tổng quát đơn hàng
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#quanlycuahang"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class=" ri-home-heart-line"></i>

                                <span data-key="t-dashboards">Quản lý cửa hàng</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="quanlycuahang">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('store', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Danh sách cửa hàng
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('pending_approval_stores', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-crm">
                                            Danh sách chờ duyệt
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('trash_stores',['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-crm">
                                            Cửa hàng đã xóa
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('violation_stores',['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-crypto">
                                            Cửa hàng vi phạm
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#quanlydanhmuc"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class="ri-align-justify"></i>
                                <span data-key="t-dashboards">Quản lý danh mục</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="quanlydanhmuc">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('list_category', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Danh sách danh mục
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('trash_category', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-ecommerce">
                                            Danh mục đã xóa
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#quanlyblog"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class="ri-terminal-window-fill"></i>
                                <span data-key="t-dashboards">Quản lý bài viết</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="quanlyblog">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('blog', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Danh sách Danh mục bài viết
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('post', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Danh sách bài viết
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#quanlynguoidung"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class="ri-dashboard-2-line"></i>
                                <span data-key="t-dashboards">Quản lý người dùng</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="quanlynguoidung">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('costomer', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Danh sách khách hàng
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('manager',['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-crm">
                                            Danh sách người quản lý
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('trash_user',['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-ecommerce">
                                            Các tài khoản đã bị xóa
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('pending_approval',['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-ecommerce">
                                            Các tài chưa được xác thực qua mail
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#quanlyphanquyen"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class="ri-git-repository-private-fill"></i>
                                <span data-key="t-dashboards">Quản lý phân quyền</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="quanlyphanquyen">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('list_role', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Quản lý phân quyền
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="#thongtinsan"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <i class=" ri-building-2-line"></i>
                                <span data-key="t-dashboards">Quản lý thông tin sàn</span>
                            </a>
                            <div
                                class="collapse menu-dropdown"
                                id="thongtinsan">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('config', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-analytics">
                                            Thông tin cơ bản của sàn
                                        </a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a
                                            href="{{ route('bannerall', ['token' => auth()->user()->refesh_token]) }}"
                                            class="nav-link"
                                            data-key="t-ecommerce">
                                            Quản lý banner
                                        </a>
                                    </li>
            
                                    
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="{{ route('product_all', ['token' => auth()->user()->refesh_token]) }}">
                                <i class="ri-checkbox-circle-line"></i> 
                                <span data-key="t-dashboards">Xét duyệt sản phẩm</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="{{ route('voucherall', ['token' => auth()->user()->refesh_token]) }}"
                                data-key="t-crm">
                                <i class="ri-gift-line"></i> 
                                <span data-key="t-dashboards">Quản lý voucher</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="{{ route('taxall', ['token' => auth()->user()->refesh_token]) }}"
                                data-key="t-projects">
                                <i class="ri-money-dollar-circle-line"></i> 
                                <span data-key="t-dashboards">Quản lý thuế</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="{{ route('rankall', ['token' => auth()->user()->refesh_token]) }}"
                                data-key="t-projects">
                                <i class="ri-bar-chart-line"></i> 
                                <span data-key="t-dashboards">Quản lý cấp bậc</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="{{ route('payment_method', ['token' => auth()->user()->refesh_token]) }}"
                                data-key="t-projects">
                                <i class="ri-bank-card-line"></i> 
                                <span data-key="t-dashboards">Phương thức thanh toán</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link menu-link"
                                href="{{ route('product_update') }}"
                                data-key="t-projects">
                                <i class="ri-bank-card-line"></i> 
                                <span data-key="t-dashboards">test</span>
                            </a>
                        </li>
                        
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                @yield('main')
                
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear());
                            </script>
                            © VNshop.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Hệ thống được thiết kế bởi đội ngũ vnshop
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
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
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/js/pages/dashboard-ecommerce.init.js"></script>

    <script src="assets/js/app.js"></script>
</body>

<!-- Mirrored from themesbrand.com/velzon/html/master/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:45:33 GMT -->

</html>
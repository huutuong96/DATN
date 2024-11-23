<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/apps-email-ecommerce.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:17 GMT -->
<head>

    <meta charset="utf-8" />
    <title>Hóa đơn điện tử VNSHOP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&amp;display=swap" rel="stylesheet">

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
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <?php 
                        $subTotal = 0;
                    ?>
                    @foreach($orders as $order)
                    <div class="row">
                        <!--end col-->
                        <div class="col-12">
                            <table class="body-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: transparent; margin: 0;">
                                <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
                                    <td class="container" width="600" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                                        <div class="content" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                                            <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                                                <tr style="font-family: 'Roboto', sans-serif; font-size: 14px; margin: 0;">
                                                    <td class="content-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; color: black; font-size: 14px; vertical-align: top; margin: 0;padding: 30px; box-shadow: 0 3px 15px rgba(30,32,37,.06); ;border-radius: 7px; background-color: #e1fcff;" valign="top">
                                                        <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 24px; vertical-align: top; margin: 0; padding: 0 0 10px; text-align: center;" valign="top">
                                                                    <h4 style="font-family: 'Roboto', sans-serif; margin-bottom: 10px; font-weight: 600;">Đặt hàng thành công</h5>
                                                                </td>
                                                            </tr>
                                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                                    <h5 style="font-family: 'Roboto', sans-serif; margin-bottom: 3px;">Xin chào, {{ $user->fullname ?? null}}</h5>
                                                                    <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">Cảm ơn bạn đã đặt hàng tại VNSHOP</p>
                                                                </td>
                                                            </tr>
                                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 18px;" valign="top">
                                                                    <table style="width:100%;">
                                                                        <tbody>
                                                                            <tr style="text-align: left;">
                                                                                <th style="padding: 5px;">
                                                                                    <p style="color: #878a99; font-size: 13px; margin-bottom: 2px; font-weight: 400;">Mã đơn hàng</p>
                                                                                    <span>{{$order->group_order_id}}</span>
                                                                                </th>
                                                                                <th style="padding: 5px;">
                                                                                    <p style="color: #878a99; font-size: 13px; margin-bottom: 2px; font-weight: 400;">Ngày đặt đơn</p>
                                                                                    <span>{{$order->created_at}}</span>
                                                                                </th>
                                                                                <th style="padding: 5px;">
                                                                                    <p style="color: #878a99; font-size: 13px; margin-bottom: 2px; font-weight: 400;">Phương thức thanh toán</p>
                                                                                    <span>{{$paymentMethod}}</span>
                                                                                </th>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                                    <h6 style="font-family: 'Roboto', sans-serif; font-size: 15px; text-decoration-line: underline;margin-bottom: 15px;">Chi tiết đơn hàng:</h6>
                                                                    <table style="width:100%;" cellspacing="0" cellpadding="0">
                                                                        <thead style="text-align: left;">
                                                                            <th style="padding: 8px;border-bottom: 1px solid #e9ebec;">Tên sản phẩm</th>
                                                                            <th style="padding: 8px;border-bottom: 1px solid #e9ebec;">Số lượng</th>
                                                                            <th style="padding: 8px;border-bottom: 1px solid #e9ebec;">Đơn giá</th>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($carts as $cart)
                                                                            <tr>
                                                                                <td style="padding: 8px; font-size: 13px;">
                                                                                    <h6 style="margin-bottom: 2px; font-size: 14px;">{{$cart->product_name  ?? null}}</h6>
                                                                                    @if($cart->variant_id != null)
                                                                                        <p style="margin-bottom: 2px; font-size: 13px; color: #878a99;">{{$cart->variant_name ?? null}}</p>
                                                                                    @endif
                                                                                </td>
                                                                                <td style="padding: 8px; font-size: 13px;">
                                                                                        <?php 
                                                                                            $cartQuantity = $cart->quantity;
                                                                                        ?>
                                                                                            {{$cart->quantity ?? 1}}
                                                                                </td>
                                                                                <td style="padding: 8px; font-size: 13px;">
                                                                                        
                                                                                    @if($cart->variant_id != null)
                                                                                        <p style="margin-bottom: 2px; font-size: 13px; color: #878a99;">{{number_format($subTotal += $cartQuantity * $cart->variant_price)}}đ</p>
                                                                                    @else
                                                                                        <p style="margin-bottom: 2px; font-size: 13px; color: #878a99;">{{number_format($subTotal += $cartQuantity * $cart->product_price)}}đ</p>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                    Tổng đơn giá
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{number_format($subTotal ?? $order->total_amount)}}đ
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                                    Phí ship
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;">
                                                                                    {{number_format($shipFee) ?? 0}}đ
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                                    Giảm giá
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;">
                                                                                    {{number_format($disscount) ?? 0}}đ
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                    Tổng tiền
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{number_format($order->total_amount) ?? 0}}đ
                                                                                </th>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                 
                                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 0px;" valign="top">
                                                                    <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">Wl'll send you shipping confirmation when your item(s) are on the way! We appreciate your business, and hope you enjoy your purchase.</p>
                                                                    <h6 style="font-family: 'Roboto', sans-serif; font-size: 14px; margin-bottom: 0px; text-align: end;">Thank you!</h6>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="margin-top: 32px; text-align: center;">
                                                <a href="#" itemprop="url" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: .8125rem; color: #FFF; text-decoration: none; font-weight: 400; text-align: center; cursor: pointer; display: inline-block; border-radius: .25rem; text-transform: capitalize; background-color: #405189; margin: 0; border-color: #405189; border-style: solid; border-width: 1px; padding: .5rem .9rem;">Download</a>
                                                <a href="#" itemprop="url" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: .8125rem; color: #FFF; text-decoration: none; font-weight: 400; text-align: center; cursor: pointer; display: inline-block; border-radius: .25rem; text-transform: capitalize; background-color: #0ab39c; margin: 0; border-color: #0ab39c; border-style: solid; border-width: 1px; padding: .5rem .9rem;">Back to Shop</a>
                                            </div>
                                            <div style="text-align: center; margin: 28px auto 0px auto;">
                                                <p style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;">2022 Velzon. Design & Develop by Themesbrand</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <!-- end table -->
                        </div>
                        <!--end col-->
                    </div><!-- end row -->
                    @endforeach
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © VNSHOP.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                VNSHOP - Ecommerce Action
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/apps-email-ecommerce.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:17 GMT -->
</html>
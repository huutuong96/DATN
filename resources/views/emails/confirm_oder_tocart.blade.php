<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/apps-email-ecommerce.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:17 GMT -->
<head>

    <meta charset="utf-8" />
    <title>Invoice Action | Velzon - Admin & Dashboard Template</title>
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
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="justify-content-between d-flex align-items-center mt-3 mb-4">
                                <h5 class="mb-0 pb-1 text-decoration-underline">Rating and Review Email Template</h5>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-12">
                            <table class="body-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: transparent; margin: 0;">
                                <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
                                    <td class="container" width="600" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                                        
                                    
                                    
                                    <div class="content" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                                           @foreach($orders as $order)
                                            <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                                                <tr style="font-family: 'Roboto', sans-serif; font-size: 14px; margin: 0;">
                                                    <td class="content-wrap" style=" font-family: 'Roboto', sans-serif; box-sizing: border-box; color: #495057; font-size: 14px; vertical-align: top; margin: 0;box-shadow: 0 3px 15px rgba(30,32,37,.06); ;border-radius: 7px; background-color: #e5e5e5;overflow: hidden;" valign="top">
                                                        <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                                        <div style="padding: 20px;box-sizing: border-box; text-align: center; background-image: linear-gradient(to right, #405189, #405189);">
                                                            <h6 style="font-family: 'Roboto', sans-serif;margin: 0; font-size: 15px;color: #fff;text-transform: uppercase;">Bạn Đã Đặt Một Đơn Hàng</h6>
                                                        </div>
                                                        <div style="padding: 20px;box-sizing: border-box; text-align: center; border-bottom: 1px solid #e9ebec;">
                                                            <img src="assets/images/logo-dark.png" alt="" height="23">
                                                        </div>
                                                        <div style="padding: 20px;box-sizing: border-box; text-align: center;">
                                                            <h5 style="font-family: 'Roboto', sans-serif;margin-bottom: 10px;font-weight: 500;">Cảm ơn bạn đã đồng hành cùng VNshop</h5>
                                                            
                                                            <p style="font-size: 14px;color: #98a6ad;border-bottom: 1px solid #e9ebec;padding-bottom: 18px;">Đơn hàng của bạn đang được xử lý và sẽ đến tay bạn trong vài ngày tới</p>
                                                        
                                                            <table style="width:100%;font-family: 'Roboto', sans-serif;">
                                                                <tbody>
                                                                    @foreach($orderDetails as $orderDetail)
                                                                        @if($orderDetail->order_id == $order->id)
                                                                            @if($orderDetail->variant_id == null)
                                                                                @foreach($products as $product)
                                                                                    @if($orderDetail->product_id == $product->id)
                                                                                        <tr style="text-align: left;">
                                                                                            <th style="padding: 5px;width: 110px;">
                                                                                                <img src="{{$product->image ?? null}}" alt="" height="80">
                                                                                            </th>
                                                                                            <th style="padding: 5px;">
                                                                                                <h6 style="font-family: 'Roboto', sans-serif; font-size: 14px; margin-bottom: 2px; font-weight: 500;">{{$product->name ?? null}}</h6>
                                                                                                <p style="color: #878a99; font-weight: 400;margin-bottom: 5px;line-height: 1.5;font-size: 12px;">Số lượng : {{$orderDetail->quantity ?? null}}</p>
                                                                                                <p style="color: #878a99; font-weight: 400;margin-bottom: 5px;line-height: 1.5;font-size: 12px;">Đơn giá : {{$orderDetail->subtotal ?? null}}</p>
                                                                                            </th>
                                                                                        </tr>
                                                                                    @endif 
                                                                                @endforeach
                                                                            @elseif($orderDetail->variant_id != null)
                                                                            @foreach($variants as $variant)
                                                                                    @if($orderDetail->variant_id == $variant->id)
                                                                                        <tr style="text-align: left;">
                                                                                            <th style="padding: 5px;width: 110px;">
                                                                                                <img src="{{$variant->images ?? null}}" alt="" height="80">
                                                                                            </th>
                                                                                            <th style="padding: 5px;">
                                                                                                <h6 style="font-family: 'Roboto', sans-serif; font-size: 14px; margin-bottom: 2px; font-weight: 500;">{{$variant->name ?? null}}</h6>
                                                                                                <p style="color: #878a99; font-weight: 400;margin-bottom: 5px;line-height: 1.5;font-size: 12px;">Số lượng : {{$orderDetail->quantity ?? null}}</p>
                                                                                                <p style="color: #878a99; font-weight: 400;margin-bottom: 5px;line-height: 1.5;font-size: 12px;">Đơn giá : {{$orderDetail->subtotal ?? null}}</p>
                                                                                            </th>
                                                                                        </tr>
                                                                                    @endif 
                                                                                @endforeach
                                                                            @endif  
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                            <table style="width:100%;" cellspacing="0" cellpadding="0">
                                                                    
                                                                        <tbody>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                    Tổng tiền hàng
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{$order->vat ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                                    VAT
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;">
                                                                                    {{$order->price_before_vat ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                                    Phí vận chuyển
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;">
                                                                                    {{$order->ship_fee ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                                    Giảm giá thành viên 
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;">
                                                                                    {{$order->disscount_by_rank ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                   Mã giảm giá Cửa hàng
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{$order->voucher_shop_disscount ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                   Mã giảm giá VNSHOP
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{$order->voucher_main_disscount ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                   Thành Tiền
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{$order->total_amount ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                                  Phương thức thanh toán
                                                                                </td>
                                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                                    {{$paymentMethod ?? null}}
                                                                                </th>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            @endforeach



                                            
                                            <div style="text-align: center; margin: 28px auto 0px auto;">
                                                <p style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;">2022 Velzon. Design & Develop by Themesbrand</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!--end col-->
                    </div><!-- end row -->

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Velzon.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by Themesbrand
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
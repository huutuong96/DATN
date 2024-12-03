<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Hàng Thành Công</title>
    <!-- Link Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
           <!-- Header -->
            <div class="card-header text-white text-center" style="background-color: #0e64c1;">
                <img sti src="https://res.cloudinary.com/dg5xvqt5i/image/upload/v1732077788/igagmdm7troprglewvnz.png" alt="Hình ảnh" style="width: 100px; height: auto; margin-left: 10px;">
                <h1 class="display-6 display-md-5 display-lg-4">
                    <b>Đặt Hàng Thành Công!</b>
                </h1>
            </div>

            <!-- Body -->
            <div class="card-body">
                <h2 class="text-center mb-4 fs-5 fs-md-4 fs-lg-3">
                    Cảm ơn bạn đã mua sắm tại Shop!
                </h2>
                @foreach($orders as $order)
                
                <!-- Order Info -->
                <div class="border-top pt-3">
                    <p><strong>Mã đơn hàng:</strong>{{$order->group_order_id}}</p>
                    <p><strong>Ngày đặt hàng:</strong> {{$order->created_at}}</p>
                    <p><strong>Phương thức thanh toán:</strong> {{$paymentMethod}}</p>
                </div>
               
                <!-- Product Details -->
                <div class="border-top pt-3">
                    <h4 class="fs-6 fs-md-5">Chi tiết sản phẩm</h4>
                    <ul class="list-group list-group-flush">
                        @foreach($orderDetails as $orderDetail)
                        @if($orderDetail->order_id == $order->id)
                            @if($orderDetail->variant_id == null)
                                @foreach($products as $product)
                                    @if($orderDetail->product_id == $product->id)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>
                                                <img style="padding: 5px;width: 110px; height: 80px;" src="{{$product->image ?? null}}" alt="" >
                                                {{$product->name ?? null}}X{{$orderDetail->quantity ?? null}}</span>
                                            <span class="pt-5">
                                                {{$orderDetail->subtotal ?? null}}
                                            </span>
                                        </li>
                                    @endif 
                                @endforeach
                            @elseif($orderDetail->variant_id != null)
                                @foreach($variants as $variant)
                                        @if($orderDetail->variant_id == $variant->id)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>
                                                <img style="padding: 5px;width: 110px; height: 80px;" src="{{$product->image ?? null}}" alt="" >
                                                {{$$variant->name ?? null}}X{{$orderDetail->quantity ?? null}}</span>
                                            <span class="pt-5">
                                                {{$orderDetail->subtotal ?? null}}
                                            </span>
                                        </li>
                                        @endif 
                                @endforeach
                            @endif
                        @endif
                        @endforeach    
                    </ul>
                <!-- Summary -->
                <div class="border-top pt-3">
                    <p class="mb-1">Tổng tiền hàng:
                        <span class="  fs-6 fs-md-5 fs-lg-4">  {{number_format($order->vat ?? null)}}đ</span>
                    </p>
                    <p class="mb-1">VAT:
                        <span class=" fs-6 fs-md-5 fs-lg-4">{{number_format($subTotal ?? $order->total_amount)}}đ</span>
                    </p>
                   
                    <p class="mb-1">Phí vận chuyển:
                        <span class="  fs-6 fs-md-5 fs-lg-4">{{number_format($shipFee) ?? 0}}đ</span>
                    </p>
                    <p class="mb-1">Giảm giá thành viên:
                        <span class="  fs-6 fs-md-5 fs-lg-4">{{$order->disscount_by_rank ?? null}}đ</span>
                    </p>
                    <p class="mb-1">Mã giảm giá cửa hàng
                        <span class="  fs-6 fs-md-5 fs-lg-4">{{$order->voucher_shop_disscount ?? null}}đ</span>
                    </p>
                    <p class="mb-1">Mã giảm giá VNSHOP
                        <span class="  fs-6 fs-md-5 fs-lg-4"> {{$order->voucher_main_disscount ?? null}}đ</span>
                    </p>
                   
                    <p class="mb-1"><strong>Tổng tiền:</strong> 
                        <span class="text-danger fw-bold fs-6 fs-md-5 fs-lg-4">  {{$order->total_amount ?? null}}đ</span>
                    </p>
                </div>
            </div>
               @endforeach
            
        </div>
    </div>

    <!-- Bootstrap 5 Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

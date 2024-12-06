<!DOCTYPE html>
<html>
<head>
    <title>Bills</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Danh sách hóa đơn</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->product_name }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->price }}</td>
                        <p><strong>Mã đơn hàng:</strong>{{$order->payment->name}}</p>
                        <p><strong>Ngày đặt hàng:</strong> {{$order->created_at}}</p>
                        {{-- <p><strong>Phương thức thanh toán:</strong> {{$paymentMethod}}</p> --}}
                    </tr>
                    @foreach($order->orderDetails as $orderDetail)
                   @dd($orderDetail);
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
                        @elseif($order->orderDetail->variant_id != null)
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
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

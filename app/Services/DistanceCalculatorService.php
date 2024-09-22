<?php

namespace App\Services;

use GuzzleHttp\Client;

class DistanceCalculatorService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('HERE_API_KEY'); // Lấy API key từ file .env
    }

    /**
     * Tính toán khoảng cách giữa hai địa điểm dựa trên tọa độ.
     * @param float $originLat
     * @param float $originLng
     * @param float $destinationLat
     * @param float $destinationLng
     * @return float|null Trả về khoảng cách tính bằng km hoặc null nếu có lỗi
     */
    public function calculateDistance($originLat, $originLng, $destinationLat, $destinationLng)
    {
        $url = 'https://router.hereapi.com/v8/routes';

        $response = $this->client->get($url, [
            'query' => [
                'apikey' => $this->apiKey,
                'transportMode' => 'car',
                'origin' => "$originLat,$originLng",
                'destination' => "$destinationLat,$destinationLng",
                'return' => 'summary',
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['routes'][0]['sections'][0]['summary']['length'])) {
            // Khoảng cách trả về là mét, chuyển đổi thành km
            return $data['routes'][0]['sections'][0]['summary']['length'] / 1000;
        }

        return null;
    }

    /**
     * Tính giá ship dựa trên khoảng cách.
     * @param float $distance
     * @return int
     */
    // public function calculateShippingFee($distance)
    // {
    //     $baseFee = 10000; // Phí ship cơ bản
    //     $extraFeePerKm = 5000; // Phí tăng thêm mỗi km sau 5km

    //     if ($distance <= 5) {
    //         return $baseFee;
    //     }

    //     return $baseFee + ($distance - 5) * $extraFeePerKm;
    // }

    // public function calculateShippingFee($distance, $locationType)
    // {
    //     $shippingRates = [
    //         'noi_thanh_hcm' => ['base' => 0, 'extra' => 0, 'threshold' => 0],
    //         'ngoai_thanh_hcm' => ['base' => 20000, 'extra' => 0, 'threshold' => 0],
    //         'tinh' => ['base' => 45000, 'extra' => 0, 'threshold' => 0],
    //     ];

    //     if (!isset($shippingRates[$locationType])) {
    //         throw new \InvalidArgumentException("Loại địa điểm không hợp lệ");
    //     }

    //     return $shippingRates[$locationType]['base'];
    // }

    public function calculateShippingFee($distance, $locationType, $shippingType = 'standard', $insuranceOptions = [])
    {
        $baseRates = [
            'noi_thanh_hcm' => 0,
            'ngoai_thanh_hcm' => 20000,
            'tinh' => 45000,
        ];

        if (!isset($baseRates[$locationType])) {
            throw new \InvalidArgumentException("Loại địa điểm không hợp lệ");
        }

        $baseFee = $baseRates[$locationType];

        $shippingMultipliers = [
            'tiet_kiem' => 1.0,
            'standard' => 1.2,
            'nhanh' => 1.5,
            'hoa_toc' => 2.0,
        ];

        if (!isset($shippingMultipliers[$shippingType])) {
            throw new \InvalidArgumentException("Loại dịch vụ giao hàng không hợp lệ");
        }

        $shippingFee = ceil($baseFee * $shippingMultipliers[$shippingType]);

        // Tính phí bảo hiểm
        $insuranceFee = $this->calculateInsuranceFee($insuranceOptions);

        return $shippingFee + $insuranceFee;
    }

    private function calculateInsuranceFee($insuranceOptions)
    {
        $insuranceRates = [
            'fragile' => 10000,  // Phí cho sản phẩm dễ vỡ
            'deformation' => 15000,  // Phí cho sản phẩm có nguy cơ méo móp
        ];
        $totalInsuranceFee = 0;
        foreach ($insuranceOptions as $option) {
            if (isset($insuranceRates[$option])) {
                $totalInsuranceFee += $insuranceRates[$option];
            }
        }
        return $totalInsuranceFee;
    }
}

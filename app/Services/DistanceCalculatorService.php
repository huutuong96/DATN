<?php

namespace App\Services;
use App\Models\ShipsModel;
use App\Models\insurance;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DistanceCalculatorService
{
    protected $client;
    protected $apiKey;
    protected $ship;
    protected $insurance;
    protected $shipping_type;
    protected $ship_company_id;
    protected $insurance_options;
    public function __construct(Request $request)
    {
        $this->insurance_options = $request->insurance ?? [];
        $this->shipping_type = $request->shipping_type;
        $this->client = new Client();
        $this->apiKey = env('HERE_API_KEY'); // Lấy API key từ file .env
        $this->insurance = $this->getInsuranceRates($request) ?? null;
    }

    private function getInsuranceRates(Request $request)
    {
        return insurance::whereIn('code', $this->insurance_options)
            ->pluck('price', 'code')
            ->toArray();
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
    private function getShippingRates($ship_id)
    {
        return ShipsModel::where('id', $ship_id)->select('fees', 'code')->first();
    }

    public function calculateShippingFee($distance, $ship_id)
    {
        $shipType = $this->getShippingRates($ship_id);// Phí cơ bản
        $baseFee = $shipType->fees;
        // Tính phí dựa trên khoảng cách
        if ($distance > 0) {
            // Tính số km đầy đủ
            $fullTens = floor($distance / 10);
            // Tính phí cho số km đầy đủ
            $additionalFee = $fullTens * 100; // 100đ cho mỗi 10 km
            $baseFee += $additionalFee;
        }

        // tính phí bảo hiểm nếu có
        if(isset($this->insurance_options)){
            $baseFee += array_sum($this->insurance);
        }
        return $baseFee;
    }


}

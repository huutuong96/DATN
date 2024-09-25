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
    protected $ships;
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
        $this->ships = $this->getShippingRates() ?? null;
        $this->insurance = $this->getInsuranceRates($request) ?? null;
    }
    private function getShippingRates()
    {
        return ShipsModel::pluck('fees', 'code')->toArray();
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


    public function calculateShippingFee($distance)
    {
        // NẾU KHÔNG CHỌN SHIP_TYPE THÌ TÍNH THEO KHOẢNG CÁCH
        $baseRates = $this->ships;
        // Xác định loại địa điểm dựa trên khoảng cách
        if(!isset($this->shipping_type) || $this->shipping_type == null){
            if ($distance <= 10) {
                $locationType = $baseRates['NOI-THANH'];
            } elseif ($distance <= 30) {
                $locationType = $baseRates['NGOAI-THANH'];
            } else {
                $locationType = $baseRates['TINH'];
            }
        }
        // NẾU CHỌN SHIP_TYPE THÌ TÍNH THEO SHIP_TYPE
        if(isset($this->shipping_type)){
            $locationType = $baseRates[$this->shipping_type];
        }

        if (!isset($locationType)) {
            throw new \InvalidArgumentException("Loại địa điểm không hợp lệ");
        }
        $baseFee = $locationType;

        $shippingMultipliers = 0;
        if(isset($this->insurance_options)){
            $shippingMultipliers = array_sum($this->insurance);
        }
        // dd($shippingMultipliers);
        if (!isset($shippingMultipliers)) {
            throw new \InvalidArgumentException("Loại dịch vụ giao hàng không hợp lệ");
        }

        $shippingFee = ceil($baseFee + $shippingMultipliers);
        return $shippingFee;
    }


}

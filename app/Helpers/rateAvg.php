<?php 
use App\Models\CommentsModel;


if (!function_exists('rateAvg')) {
    function rateAvg($id)
    {
        
        $Comments = CommentsModel::where('rate', '!=', null)->where('product_id', $id)->get();
        $data = 0;
        foreach ($Comments as $Comment) {
            $data += $Comment->rate;
        }
        $rateAvg = count($Comments) > 0 ? $data / count($Comments) : 0;
        return $rateAvg;
    }
}

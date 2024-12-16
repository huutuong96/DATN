<?php
namespace App\Services;

use Phpml\Classification\KNearestNeighbors;

class RecommendationService
{
    public function recommendTopN(array $userVector, array $trainingData, array $labels, int $n)
    {
        $classifier = new KNearestNeighbors();
        $classifier->train($trainingData, $labels);
        
        $distances = [];
        foreach ($trainingData as $index => $data) {
            $distance = $this->calculateDistance($userVector, $data);
            $distances[] = ['distance' => $distance, 'label' => $labels[$index]];
        }

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $uniqueLabels = [];
        foreach ($distances as $item) {
            if (!in_array($item['label'], $uniqueLabels)) {
                $uniqueLabels[] = $item['label'];
            }
            if (count($uniqueLabels) >= $n) {
                break;
            }
        }

        return $uniqueLabels;
    }

    private function calculateDistance(array $a, array $b)
    {
        return sqrt(array_sum(array_map(function ($x, $y) {
            return is_numeric($x) && is_numeric($y) ? pow($x - $y, 2) : 0;
        }, $a, $b)));
    }
}

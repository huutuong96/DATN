<?php 

use Phpml\Classification\KNearestNeighbors;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

function checkProductDescription($description)
{
    // Dữ liệu huấn luyện (sản phẩm phù hợp và không phù hợp)
    $samples = [
        ['product contains illegal drug', 'inappropriate'],
        ['product is safe and legal', 'appropriate'],
        ['contains fake brand', 'inappropriate'],
    ];
    $labels = ['inappropriate', 'appropriate', 'inappropriate'];

    // Huấn luyện mô hình
    $classifier = new KNearestNeighbors();
    $classifier->train($samples, $labels);

    // Kiểm tra sản phẩm
    return $classifier->predict([$description]);
}

$result = checkProductDescription("This product contains fake brand material");
echo $result; // inappropriate



function extractTextFromURL($imageUrl)
{
    $imageAnnotator = new ImageAnnotatorClient();

    // Lấy nội dung ảnh từ URL
    $image = file_get_contents($imageUrl);

    // Gửi yêu cầu OCR
    $response = $imageAnnotator->textDetection($image);
    $texts = $response->getTextAnnotations();

    // Lấy nội dung văn bản đầu tiên (nếu có)
    $text = $texts[0]->getDescription() ?? 'No text found';

    $imageAnnotator->close(); // Đóng kết nối

    return $text;
}

// Link ảnh cần phân tích
$imageUrl = 'https://example.com/image.jpg';
$text = extractTextFromURL($imageUrl);

echo $text;

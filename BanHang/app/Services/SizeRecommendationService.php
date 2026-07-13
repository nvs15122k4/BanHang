<?php

namespace App\Services;

/**
 * SizeRecommendationService
 * 
 * Service để gợi ý kích cỡ dựa vào chiều cao, cân nặng và loại sản phẩm
 * Dùng tiêu chuẩn kích cỡ Châu Á
 */
class SizeRecommendationService
{
    /**
     * Size mapping dựa vào chiều cao + cân nặng
     * Tiêu chuẩn Châu Á: XS, S, M, L, XL, XXL
     */
    private const SIZE_RANGES = [
        'XS' => [
            'height_range' => [0, 155],
            'weight_range' => [0, 45],
            'bmi_range' => [0, 18.5],
            'description' => 'Extra Small - Người nhỏ nhắn'
        ],
        'S' => [
            'height_range' => [155, 165],
            'weight_range' => [45, 55],
            'bmi_range' => [18.5, 21],
            'description' => 'Small - Người nhỏ'
        ],
        'M' => [
            'height_range' => [165, 175],
            'weight_range' => [55, 68],
            'bmi_range' => [21, 24],
            'description' => 'Medium - Bình thường'
        ],
        'L' => [
            'height_range' => [175, 185],
            'weight_range' => [68, 82],
            'bmi_range' => [24, 27],
            'description' => 'Large - Hơi to'
        ],
        'XL' => [
            'height_range' => [185, 195],
            'weight_range' => [82, 100],
            'bmi_range' => [27, 30],
            'description' => 'Extra Large - To'
        ],
        'XXL' => [
            'height_range' => [195, 300],
            'weight_range' => [100, 10000],
            'bmi_range' => [30, 100],
            'description' => 'Double Extra Large - Rất to'
        ]
    ];

    /**
     * Tính BMI từ cân nặng và chiều cao
     * 
     * @param float $weight Cân nặng (kg)
     * @param int $height Chiều cao (cm)
     * @return float BMI
     */
    public function calculateBMI(float $weight, int $height): float
    {
        if ($height == 0) {
            return 0;
        }
        $heightInMeters = $height / 100;
        return round($weight / ($heightInMeters ** 2), 2);
    }

    /**
     * Gợi ý size dựa vào height + weight
     * 
     * @param int $height Chiều cao (cm)
     * @param float $weight Cân nặng (kg)
     * @return array|null Size recommendations
     */
    public function recommendSize($height, $weight)
    {
        if (!$height || !$weight) {
            return null;
        }

        $height = (int) $height;
        $weight = (float) $weight;
        $bmi = $this->calculateBMI($weight, $height);

        $recommendations = [];
        $primarySize = null;

        // Kiểm tra từng size dựa vào height + weight + BMI
        foreach (self::SIZE_RANGES as $size => $ranges) {
            $heightMatch = $height >= $ranges['height_range'][0] && $height <= $ranges['height_range'][1];
            $weightMatch = $weight >= $ranges['weight_range'][0] && $weight <= $ranges['weight_range'][1];
            $bmiMatch = $bmi >= $ranges['bmi_range'][0] && $bmi <= $ranges['bmi_range'][1];

            // Score: dựa vào số điều kiện khớp
            $score = 0;
            if ($heightMatch) $score += 1;
            if ($weightMatch) $score += 1;
            if ($bmiMatch) $score += 1;

            if ($score > 0) {
                $recommendations[] = [
                    'size' => $size,
                    'score' => $score,
                    'matches' => [
                        'height' => $heightMatch,
                        'weight' => $weightMatch,
                        'bmi' => $bmiMatch,
                    ],
                    'description' => $ranges['description']
                ];
            }
        }

        // Sắp xếp theo score (cao nhất trước)
        usort($recommendations, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        if (empty($recommendations)) {
            return null;
        }

        // Recommend size đầu tiên (score cao nhất)
        $primarySize = $recommendations[0]['size'];

        // Lấy top 3 size gợi ý
        $topRecommendations = array_slice($recommendations, 0, 3);

        return [
            'primary_size' => $primarySize,
            'all_sizes' => $topRecommendations,
            'bmi' => $bmi,
            'height' => $height,
            'weight' => $weight,
        ];
    }

    /**
     * Lấy tất cả sizes có sẵn
     * 
     * @return array
     */
    public function getAllSizes(): array
    {
        return array_keys(self::SIZE_RANGES);
    }

    /**
     * Lấy thông tin chi tiết của size
     * 
     * @param string $size
     * @return array|null
     */
    public function getSizeInfo(string $size): ?array
    {
        return self::SIZE_RANGES[$size] ?? null;
    }

    /**
     * Giải thích recommendation
     * 
     * @param array $recommendation
     * @return string
     */
    public function getRecommendationExplanation(array $recommendation): string
    {
        $bmi = $recommendation['bmi'];
        $height = $recommendation['height'];
        $weight = $recommendation['weight'];
        $primarySize = $recommendation['primary_size'];

        $bmiCategory = 'bình thường';
        if ($bmi < 18.5) {
            $bmiCategory = 'gầy';
        } elseif ($bmi >= 18.5 && $bmi < 24) {
            $bmiCategory = 'bình thường';
        } elseif ($bmi >= 24 && $bmi < 27) {
            $bmiCategory = 'hơi béo';
        } else {
            $bmiCategory = 'béo';
        }

        return "Dựa vào chiều cao {$height}cm, cân nặng {$weight}kg (BMI: {$bmi} - $bmiCategory), chúng tôi gợi ý size {$primarySize} cho bạn.";
    }
}

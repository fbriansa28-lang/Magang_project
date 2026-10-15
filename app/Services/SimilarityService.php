<?php

namespace App\Services;

class SimilarityService
{
    public function hitungSimilarity(string $jawaban, string $kunciJawaban): float
    {
        $jawabanNormal = mb_strtolower(trim($jawaban));
        $kunciNormal = mb_strtolower(trim($kunciJawaban));

        if ($kunciNormal === '') {
            return 0.0;
        }

        similar_text($jawabanNormal, $kunciNormal, $percent);

        return round($percent / 100, 4);
    }
}
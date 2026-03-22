<?php

namespace App\Utilities;

class DocumentChunker
{
    /**
     * Splits text into overlapping chunks.
     */
    public static function chunk(string $text, int $chunkSize = 1000, int $overlap = 200): array
    {
        $chunks = [];
        $length = mb_strlen($text);
        
        if ($length <= $chunkSize) {
            return [$text];
        }

        $start = 0;
        while ($start < $length) {
            $end = min($start + $chunkSize, $length);
            
            // Try to avoid cutting words in the middle
            if ($end < $length) {
                $lastSpace = mb_strrpos(mb_substr($text, $start, $chunkSize), ' ');
                if ($lastSpace !== false && $lastSpace > ($chunkSize * 0.8)) {
                    $end = $start + $lastSpace;
                }
            }
            
            $chunks[] = mb_substr($text, $start, $end - $start);
            
            $start = $end - $overlap;
            
            // Prevent infinite loop if overlap is too large or progress is zero
            if ($start >= $end || $end >= $length) {
                break;
            }
        }

        return $chunks;
    }
}

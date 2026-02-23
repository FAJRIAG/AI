<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiKeyManager
{
    private const CACHE_KEY = 'ai_api_key_index';
    private const MAX_KEYS = 10;

    /**
     * Get the current active API key.
     */
    public function getCurrentKey(): ?string
    {
        $keys = $this->getAllKeys();
        if (empty($keys)) {
            return null;
        }

        $index = $this->getCurrentIndex();

        // Ensure index is within bounds if keys were removed
        if ($index >= count($keys)) {
            $index = 0;
            $this->setCurrentIndex(0);
        }

        return $keys[$index];
    }

    /**
     * Rotate to the next available API key.
     */
    public function rotateKey(): ?string
    {
        $keys = $this->getAllKeys();
        if (empty($keys)) {
            return null;
        }

        $currentIndex = $this->getCurrentIndex();
        $nextIndex = ($currentIndex + 1) % count($keys);

        $this->setCurrentIndex($nextIndex);

        Log::info("AI API Key rotated from index $currentIndex to $nextIndex.");

        return $keys[$nextIndex];
    }

    /**
     * Get all available API keys from environment variables.
     */
    private function getAllKeys(): array
    {
        $keys = [];

        // Check for AI_API_KEY_1 to AI_API_KEY_10
        for ($i = 1; $i <= self::MAX_KEYS; $i++) {
            $key = config("ai.api_key_$i");
            if ($key) {
                $keys[] = $key;
            }
        }

        // Fallback to legacy AI_API_KEY if no numbered keys are found
        if (empty($keys)) {
            $legacyKey = config('ai.api_key');
            if ($legacyKey) {
                $keys[] = $legacyKey;
            }
        }

        return $keys;
    }

    /**
     * Get the current key index from cache.
     */
    private function getCurrentIndex(): int
    {
        return (int) Cache::get(self::CACHE_KEY, 0);
    }

    /**
     * Set the current key index in cache.
     */
    private function setCurrentIndex(int $index): void
    {
        Cache::forever(self::CACHE_KEY, $index);
    }
}

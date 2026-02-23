<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AiKeyManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AiKeyManagerTest extends TestCase
{
    public function test_it_retrieves_current_key()
    {
        // Mock configuration
        config(['ai.api_key_1' => 'key1']);
        config(['ai.api_key_2' => 'key2']);

        $manager = new AiKeyManager();
        $this->assertEquals('key1', $manager->getCurrentKey());
    }

    public function test_it_rotates_keys()
    {
        // Mock configuration
        config(['ai.api_key_1' => 'key1']);
        config(['ai.api_key_2' => 'key2']);

        Cache::shouldReceive('get')->with('ai_api_key_index', 0)->andReturn(0);
        Cache::shouldReceive('forever')->with('ai_api_key_index', 1)->once();

        $manager = new AiKeyManager();
        $nextKey = $manager->rotateKey();

        $this->assertEquals('key2', $nextKey);
    }

    public function test_it_falls_back_to_legacy_key()
    {
        // Clear numbered keys and mock legacy key in config
        for ($i = 1; $i <= 10; $i++) {
            config(["ai.api_key_$i" => null]);
            putenv("AI_API_KEY_$i"); // Also ensure env is clear
        }
        config(['ai.api_key' => 'legacy_key']);
        putenv('AI_API_KEY'); // Ensure env is clear

        $manager = new AiKeyManager();
        $this->assertEquals('legacy_key', $manager->getCurrentKey());
    }
}

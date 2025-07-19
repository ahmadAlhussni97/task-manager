<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class QuoteService
{
    private const API_URL = 'https://api.quotable.io/random';
    private const CACHE_KEY = 'motivational_quote';
    private const CACHE_DURATION = 3600; // 1 hour

    /**
     * Fetch a random motivational quote from the API
     */
    public function getRandomQuote(): array
    {
        try {
            $response = Http::timeout(10)->get(self::API_URL);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'content' => $data['content'] ?? '',
                    'author' => $data['author'] ?? 'Unknown',
                    'tags' => $data['tags'] ?? [],
                    'success' => true
                ];
            }
            
            return [
                'content' => 'The only way to do great work is to love what you do.',
                'author' => 'Steve Jobs',
                'tags' => ['motivation', 'success'],
                'success' => false
            ];
            
        } catch (\Exception $e) {
            return [
                'content' => 'Success is not final, failure is not fatal: it is the courage to continue that counts.',
                'author' => 'Winston Churchill',
                'tags' => ['motivation', 'perseverance'],
                'success' => false
            ];
        }
    }

    /**
     * Get cached quote or fetch new one
     */
    public function getCachedQuote(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return $this->getRandomQuote();
        });
    }

    /**
     * Force refresh quote (clear cache and fetch new)
     */
    public function refreshQuote(): array
    {
        Cache::forget(self::CACHE_KEY);
        return $this->getRandomQuote();
    }
} 
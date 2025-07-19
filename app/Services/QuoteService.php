<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            $response = Http::timeout(10)
                ->withoutVerifying() // Disable SSL certificate verification
                ->get(self::API_URL);
            
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
            // Log the error for debugging
            Log::error('Quote API error: ' . $e->getMessage());
            
            return [
                'content' => 'The only way to do great work is to love what you do.',
                'author' => 'Steve Jobs',
                'tags' => ['motivation', 'success'],
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
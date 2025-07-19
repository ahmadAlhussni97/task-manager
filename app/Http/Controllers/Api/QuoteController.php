<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    protected QuoteService $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * Get a random quote from external API
     */
    public function getRandomQuote(): JsonResponse
    {
        try {
            $quote = $this->quoteService->getRandomQuote();
            
            return response()->json([
                'success' => true,
                'data' => $quote,
                'message' => 'Quote fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a cached quote
     */
    public function getCachedQuote(): JsonResponse
    {
        try {
            $quote = $this->quoteService->getCachedQuote();
            
            return response()->json([
                'success' => true,
                'data' => $quote,
                'message' => 'Cached quote retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cached quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh quote (clear cache and fetch new)
     */
    public function refreshQuote(): JsonResponse
    {
        try {
            $quote = $this->quoteService->refreshQuote();
            
            return response()->json([
                'success' => true,
                'data' => $quote,
                'message' => 'Quote refreshed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quote with specific parameters
     */
    public function getQuoteWithParams(Request $request): JsonResponse
    {
        try {
            $params = $request->validate([
                'tags' => 'nullable|string',
                'maxLength' => 'nullable|integer|min:1|max:300',
                'minLength' => 'nullable|integer|min:1|max:300',
            ]);

            $quote = $this->quoteService->getQuoteWithParams($params);
            
            return response()->json([
                'success' => true,
                'data' => $quote,
                'message' => 'Quote fetched with parameters successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quote with parameters',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): Response
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('status', '');
        $tagFilters = $request->get('tags', []);
        
        // Handle single tag filter for backward compatibility
        if ($request->has('tag') && !$request->has('tags')) {
            $tagFilters = [$request->get('tag')];
        }
        
        $perPage = $request->get('per_page', 10); // Default 10 items per page
        $page = $request->get('page', 1); // Current pagel items   
        
        $tasks = Task::where('user_id', Auth::id())
            ->when($search, function ($query) use ($search) {
                $query->search($search);
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($tagFilters, function ($query) use ($tagFilters) {
                $query->where(function ($q) use ($tagFilters) {
                    foreach ($tagFilters as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Get available tags for filter dropdown
        $availableTags = Task::where('user_id', Auth::id())
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();


        // Get motivational quote
        $quoteService = new QuoteService();
        $quote = $quoteService->getCachedQuote();

        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'tagFilters' => $tagFilters,
            'availableTags' => $availableTags,
            'quote' => $quote
        ]);
    }

    /**
     * Refresh the motivational quote
     */
    public function refreshQuote()
    {
        $quoteService = new QuoteService();
        $quote = $quoteService->refreshQuote();
        
        return response()->json($quote);
    }
} 
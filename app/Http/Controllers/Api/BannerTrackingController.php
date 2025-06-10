<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BannerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BannerTrackingController extends Controller
{
    public function __construct(
        private BannerService $bannerService
    ) {}

    /**
     * Track banner interactions (clicks, impressions)
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'banner_id' => 'required|integer|exists:banners,id',
            'action' => 'required|string|in:click,impression'
        ]);

        $tracked = $this->bannerService->trackBannerInteraction(
            $request->banner_id,
            $request->action
        );

        return response()->json([
            'success' => $tracked,
            'message' => $tracked ? 'Interaction tracked successfully' : 'Failed to track interaction'
        ]);
    }

    /**
     * Get banner analytics (for admin dashboard)
     */
    public function analytics(Request $request): JsonResponse
    {
        // This would require implementing analytics storage
        // For now, return basic stats from BannerService
        $stats = $this->bannerService->getBannerStats();
        
        return response()->json([
            'stats' => $stats
        ]);
    }
}
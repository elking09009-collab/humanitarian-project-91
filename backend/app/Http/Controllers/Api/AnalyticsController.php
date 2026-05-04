<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NeedsPredictorService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(private readonly NeedsPredictorService $predictorService)
    {
    }

    public function predictions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->predictorService->predictNextMonth(),
        ]);
    }
}

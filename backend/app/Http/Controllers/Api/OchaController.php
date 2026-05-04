<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OchaApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OchaController extends Controller
{
    public function __construct(private readonly OchaApiService $ochaApiService)
    {
    }

    public function reports(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);
        $limit = max(1, min($limit, 50));

        $result = $this->ochaApiService->latestReports($limit);

        return response()->json($result, $result['status']);
    }
}

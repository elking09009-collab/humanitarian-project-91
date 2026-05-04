<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\NeedController;
use App\Http\Controllers\Api\NeedCommentController;
use App\Http\Controllers\Api\OchaController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\SkillDonationController;
use App\Http\Controllers\Api\CharityFundController;
use App\Http\Controllers\Api\MicroProjectController;
use App\Http\Controllers\Api\VolunteerValidationController;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/registration-status', [AuthController::class, 'registrationStatus']);
Route::get('/areas', [AreaController::class, 'index']);
Route::get('/areas/{id}', [AreaController::class, 'show']);
Route::get('/needs', [NeedController::class, 'index']);
Route::get('/needs/pending', [NeedController::class, 'pending']);
Route::get('/needs/{id}', [NeedController::class, 'show']);
Route::get('/ocha/reports', [OchaController::class, 'reports']);
Route::get('/analytics/predictions', [AnalyticsController::class, 'predictions']);
Route::get('/donations/verify/{id}', [DonationController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/needs', [NeedController::class, 'store']);
    Route::post('/needs/{need}/comments', [NeedCommentController::class, 'store']);
    Route::delete('/comments/{comment}', [NeedCommentController::class, 'destroy']);

    // تسجيل FCM token للجهاز
    Route::post('/fcm-token', function (\Illuminate\Http\Request $request) {
        $request->validate(['token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->token]);
        return response()->json(['message' => 'تم تسجيل token الإشعارات']);
    });

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // ===== New features - authenticated =====
    Route::post('/skill-donations', [SkillDonationController::class, 'store']);
    Route::post('/charity-funds', [CharityFundController::class, 'store']);
    Route::post('/charity-funds/{id}/contribute', [CharityFundController::class, 'contribute']);
    Route::post('/micro-projects/{id}/fund', [MicroProjectController::class, 'fund']);
    Route::post('/volunteer-validations', [VolunteerValidationController::class, 'store']);
});

// Public comments read
Route::get('/needs/{need}/comments', [NeedCommentController::class, 'index']);

// ===== New features - public =====
Route::get('/skill-donations', [SkillDonationController::class, 'index']);
Route::get('/charity-funds', [CharityFundController::class, 'index']);
Route::post('/charity-funds/join', [CharityFundController::class, 'joinByCode']);
Route::get('/micro-projects', [MicroProjectController::class, 'index']);
Route::get('/volunteer-validations', [VolunteerValidationController::class, 'index']);
Route::get('/heatmap', [VolunteerValidationController::class, 'heatmap']);

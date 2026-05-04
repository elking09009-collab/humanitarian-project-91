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
use App\Http\Controllers\Api\SmartInventoryController;
use App\Http\Controllers\Api\MicroEndowmentController;
use App\Http\Controllers\Api\CrisisAlertController;
use App\Http\Controllers\Api\ImpactWallController;
use App\Http\Controllers\Api\EmergencyCaseController;
use App\Http\Controllers\Api\LegacyGivingController;
use App\Http\Controllers\Api\CsrCompanyController;
use App\Http\Controllers\Api\HumanTwinController;

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
    Route::get('/my-wills', [LegacyGivingController::class, 'myWills']);
    Route::get('/my-points', function (Request $request) {
        return response()->json(['points' => $request->user()->loyalty_points ?? 0]);
    });
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

// ===== Smart Inventory =====
Route::get('/inventory', [SmartInventoryController::class, 'index']);
Route::post('/inventory', [SmartInventoryController::class, 'store']);
Route::post('/inventory/{id}/reserve', [SmartInventoryController::class, 'reserve']);

// ===== Micro Endowments =====
Route::get('/endowments', [MicroEndowmentController::class, 'index']);
Route::post('/endowments', [MicroEndowmentController::class, 'store']);
Route::post('/endowments/{id}/contribute', [MicroEndowmentController::class, 'contribute']);
Route::get('/loans', [MicroEndowmentController::class, 'loans']);
Route::post('/loans', [MicroEndowmentController::class, 'applyLoan']);

// ===== Crisis Predictor =====
Route::get('/crisis-alerts', [CrisisAlertController::class, 'index']);
Route::post('/crisis-alerts/{id}/donate', [CrisisAlertController::class, 'donate']);

// ===== Impact Wall =====
Route::get('/stories', [ImpactWallController::class, 'stories']);
Route::post('/stories/{id}/like', [ImpactWallController::class, 'likeStory']);
Route::get('/forum', [ImpactWallController::class, 'forumPosts']);
Route::post('/forum', [ImpactWallController::class, 'createForumPost']);

// ===== Emergency SOS =====
Route::get('/emergency', [EmergencyCaseController::class, 'index']);
Route::post('/emergency/{id}/donate', [EmergencyCaseController::class, 'donate']);

// ===== Legacy Giving =====
Route::post('/legacy', [LegacyGivingController::class, 'store']);

// ===== CSR Companies =====
Route::get('/csr', [CsrCompanyController::class, 'index']);
Route::post('/csr/register', [CsrCompanyController::class, 'register']);

// ===== Human Twin (التوأمة الإنسانية) =====
Route::get('/twin/families',            [HumanTwinController::class, 'families']);
Route::post('/twin/families',           [HumanTwinController::class, 'registerFamily']);
Route::get('/twin/supporters',          [HumanTwinController::class, 'supporters']);
Route::post('/twin/supporters',         [HumanTwinController::class, 'registerSupporter']);
Route::get('/twin/messages/{sid}/{fid}', [HumanTwinController::class, 'messages']);
Route::post('/twin/messages',           [HumanTwinController::class, 'sendMessage']);
Route::get('/twin/stats',               [HumanTwinController::class, 'stats']);

<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegacyGiving;
use Illuminate\Http\Request;

class LegacyGivingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'             => 'required|string|max:150',
            'national_id'           => 'nullable|string|max:30',
            'amount'                => 'nullable|numeric|min:0',
            'percentage'            => 'nullable|numeric|min:0|max:100',
            'trigger_event'         => 'required|in:death,incapacity,scheduled',
            'beneficiary_category'  => 'nullable|string|max:100',
            'notes'                 => 'nullable|string',
        ]);
        if (auth('sanctum')->check()) $data['user_id'] = auth('sanctum')->id();
        $giving = LegacyGiving::create($data);
        return response()->json([
            'message' => 'تم تسجيل وصيتك الرقمية — جزاك الله خيراً',
            'giving'  => $giving
        ], 201);
    }

    public function myWills(Request $request)
    {
        $wills = LegacyGiving::where('user_id', $request->user()->id)->latest()->get();
        return response()->json($wills);
    }
}

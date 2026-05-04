<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrisisAlert;
use Illuminate\Http\Request;

class CrisisAlertController extends Controller
{
    public function index()
    {
        $alerts = CrisisAlert::where('status', 'active')
            ->orderByRaw("CASE severity WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->get()
            ->map(function ($a) {
                $a->progress = $a->needed_amount > 0
                    ? round(($a->current_amount / $a->needed_amount) * 100, 1)
                    : 0;
                return $a;
            });
        return response()->json($alerts);
    }

    public function donate(Request $request, $id)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:1']);
        $alert = CrisisAlert::findOrFail($id);
        $alert->increment('current_amount', $data['amount']);
        if ($alert->current_amount >= $alert->needed_amount) {
            $alert->update(['status' => 'resolved']);
        }
        return response()->json(['message' => 'شكراً — تم تسجيل تبرعك', 'alert' => $alert->fresh()]);
    }
}

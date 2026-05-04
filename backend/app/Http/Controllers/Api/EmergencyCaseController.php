<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyCase;
use Illuminate\Http\Request;

class EmergencyCaseController extends Controller
{
    public function index()
    {
        $cases = EmergencyCase::where('status', 'urgent')
            ->orderByDesc('is_pinned')
            ->orderBy('deadline')
            ->get()
            ->map(function ($c) {
                $c->progress = $c->needed_amount > 0
                    ? round(($c->current_amount / $c->needed_amount) * 100, 1)
                    : 0;
                $c->remaining = max(0, $c->needed_amount - $c->current_amount);
                return $c;
            });
        return response()->json($cases);
    }

    public function donate(Request $request, $id)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:1']);
        $case = EmergencyCase::findOrFail($id);
        $case->increment('current_amount', $data['amount']);
        if ($case->current_amount >= $case->needed_amount) {
            $case->update(['status' => 'funded']);
        }
        // Award loyalty points
        if (auth('sanctum')->check()) {
            $points = max(1, (int)($data['amount'] / 10));
            auth('sanctum')->user()->increment('loyalty_points', $points);
        }
        return response()->json(['message' => 'تم تسجيل تبرعك بنجاح 🙏', 'case' => $case->fresh()]);
    }
}

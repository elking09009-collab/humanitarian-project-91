<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\CharityFund;
use App\Models\FundContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CharityFundController extends Controller
{
    public function index()
    {
        return CharityFund::with('creator:id,name')->where('status','active')->latest()->get()->map(fn($f) => [
            'id'           => $f->id,
            'name'         => $f->name,
            'description'  => $f->description,
            'project_type' => $f->project_type,
            'goal_amount'  => $f->goal_amount,
            'current_amount' => $f->current_amount,
            'percent'      => $f->goal_amount > 0 ? round(($f->current_amount / $f->goal_amount) * 100) : 0,
            'status'       => $f->status,
            'invite_code'  => $f->invite_code,
            'creator_name' => $f->creator?->name ?? '-',
            'contributors' => $f->contributions()->count(),
            'created_at'   => $f->created_at?->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'description'  => 'nullable|string|max:1000',
            'project_type' => 'required|in:well,village,school,medical,other',
            'goal_amount'  => 'required|integer|min:100',
        ]);
        $data['creator_id']  = Auth::id();
        $data['invite_code'] = strtoupper(Str::random(8));
        $fund = CharityFund::create($data);
        return response()->json($fund, 201);
    }

    public function contribute(Request $request, $id)
    {
        $fund = CharityFund::findOrFail($id);
        abort_if($fund->status !== 'active', 422, 'الصندوق مغلق');
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:200',
        ]);
        FundContribution::create([
            'fund_id' => $fund->id,
            'user_id' => Auth::id(),
            'amount'  => $data['amount'],
            'note'    => $data['note'] ?? null,
        ]);
        $fund->increment('current_amount', $data['amount']);
        if ($fund->fresh()->current_amount >= $fund->goal_amount) {
            $fund->update(['status' => 'completed']);
        }
        return response()->json(['message' => 'تم تسجيل تبرعك', 'current_amount' => $fund->fresh()->current_amount]);
    }

    public function joinByCode(Request $request)
    {
        $request->validate(['invite_code' => 'required|string']);
        $fund = CharityFund::where('invite_code', strtoupper($request->invite_code))->firstOrFail();
        return response()->json($fund);
    }
}

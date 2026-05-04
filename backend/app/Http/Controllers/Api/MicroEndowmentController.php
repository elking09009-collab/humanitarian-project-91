<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MicroEndowment;
use App\Models\GoodLoan;
use Illuminate\Http\Request;

class MicroEndowmentController extends Controller
{
    public function index()
    {
        $endowments = MicroEndowment::withCount([])->latest()->get()->map(function ($e) {
            $e->progress = $e->goal_amount > 0
                ? round(($e->current_amount / $e->goal_amount) * 100, 1)
                : 0;
            return $e;
        });
        $loans = GoodLoan::latest()->limit(10)->get();
        return response()->json(['endowments' => $endowments, 'loans' => $loans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:200',
            'description'           => 'nullable|string',
            'type'                  => 'required|in:waqf,loan',
            'goal_amount'           => 'required|numeric|min:100',
            'beneficiary_category'  => 'nullable|string|max:100',
        ]);
        if (auth('sanctum')->check()) $data['creator_id'] = auth('sanctum')->id();
        $endowment = MicroEndowment::create($data);
        return response()->json(['message' => 'تم إنشاء الصندوق', 'endowment' => $endowment], 201);
    }

    public function contribute(Request $request, $id)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:1']);
        $endowment = MicroEndowment::findOrFail($id);
        $endowment->increment('current_amount', $data['amount']);
        return response()->json(['message' => 'شكراً لمساهمتك', 'endowment' => $endowment->fresh()]);
    }

    public function loans()
    {
        return response()->json(GoodLoan::latest()->paginate(10));
    }

    public function applyLoan(Request $request)
    {
        $data = $request->validate([
            'borrower_name' => 'required|string|max:150',
            'amount'        => 'required|numeric|min:100',
            'purpose'       => 'required|string',
            'endowment_id'  => 'nullable|integer',
        ]);
        $loan = GoodLoan::create($data);
        return response()->json(['message' => 'تم تقديم طلب القرض الحسن', 'loan' => $loan], 201);
    }
}

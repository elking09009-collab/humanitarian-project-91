<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SkillDonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillDonationController extends Controller
{
    public function index()
    {
        return SkillDonation::with('donor:id,name')->latest()->get()->map(fn($s) => [
            'id'           => $s->id,
            'skill_type'   => $s->skill_type,
            'skill_title'  => $s->skill_title,
            'description'  => $s->description,
            'hours_offered'=> $s->hours_offered,
            'status'       => $s->status,
            'donor_name'   => $s->donor?->name ?? 'متبرع',
            'created_at'   => $s->created_at?->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'skill_type'   => 'required|in:medical,teaching,tech,legal,other',
            'skill_title'  => 'required|string|max:100',
            'description'  => 'required|string|max:1000',
            'hours_offered'=> 'required|integer|min:1|max:100',
            'contact_info' => 'nullable|string|max:200',
            'need_id'      => 'nullable|exists:needs,id',
        ]);
        $data['donor_id'] = Auth::id();
        $skill = SkillDonation::create($data);
        return response()->json($skill, 201);
    }
}

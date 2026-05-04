<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\MicroProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MicroProjectController extends Controller
{
    public function index()
    {
        return MicroProject::with('beneficiary:id,name','area')->latest()->get()->map(fn($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'description'   => $p->description,
            'category'      => $p->category,
            'target_amount' => $p->target_amount,
            'funded_amount' => $p->funded_amount,
            'percent'       => $p->target_amount > 0 ? round(($p->funded_amount / $p->target_amount) * 100) : 0,
            'status'        => $p->status,
            'image_url'     => $p->image_url,
            'beneficiary'   => $p->beneficiary?->name ?? '-',
            'area_name'     => $p->area ? $p->area->getTranslation('name','ar',false) : null,
            'created_at'    => $p->created_at?->format('Y-m-d'),
        ]);
    }

    public function fund(Request $request, $id)
    {
        $project = MicroProject::findOrFail($id);
        abort_if($project->status === 'funded', 422, 'المشروع مكتمل التمويل');
        $data = $request->validate(['amount' => 'required|integer|min:1']);
        $project->increment('funded_amount', $data['amount']);
        if ($project->fresh()->funded_amount >= $project->target_amount) {
            $project->update(['status' => 'funded']);
        }
        return response()->json(['message' => 'شكراً! تم تسجيل دعمك للمشروع', 'funded_amount' => $project->fresh()->funded_amount]);
    }
}

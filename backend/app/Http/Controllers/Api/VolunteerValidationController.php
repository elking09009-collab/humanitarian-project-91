<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\VolunteerValidation;
use App\Models\Need;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerValidationController extends Controller
{
    public function index()
    {
        // Public: list validated needs (approved only)
        return VolunteerValidation::with('need','volunteer:id,name')->where('status','approved')->latest()->get()->map(fn($v) => [
            'id'           => $v->id,
            'need_id'      => $v->need_id,
            'need_type'    => $v->need?->type,
            'field_notes'  => $v->field_notes,
            'document_urls'=> $v->document_urls,
            'volunteer'    => $v->volunteer?->name,
            'created_at'   => $v->created_at?->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'need_id'      => 'required|exists:needs,id',
            'field_notes'  => 'required|string|max:2000',
            'document_urls'=> 'nullable|array',
            'document_urls.*' => 'url',
        ]);
        $data['volunteer_id'] = Auth::id();
        $validation = VolunteerValidation::create($data);
        return response()->json(['message' => 'تم رفع التوثيق بنجاح وهو قيد المراجعة', 'id' => $validation->id], 201);
    }

    // GET /api/heatmap — aggregate needs per area for heat map
    public function heatmap()
    {
        $data = \DB::table('needs')
            ->join('areas', 'needs.area_id', '=', 'areas.id')
            ->select('areas.id','areas.latitude','areas.longitude','areas.name as area_name_json','areas.priority_level',
                \DB::raw('COUNT(needs.id) as needs_count'),
                \DB::raw("SUM(CASE WHEN needs.status='pending' THEN 1 ELSE 0 END) as pending_count"),
                \DB::raw("SUM(needs.quantity) as total_quantity")
            )
            ->groupBy('areas.id','areas.latitude','areas.longitude','areas.name','areas.priority_level')
            ->get()
            ->map(fn($row) => [
                'area_id'       => $row->id,
                'lat'           => (float) $row->latitude,
                'lng'           => (float) $row->longitude,
                'area_name'     => json_decode($row->area_name_json, true)['ar'] ?? $row->area_name_json,
                'priority'      => $row->priority_level,
                'needs_count'   => (int) $row->needs_count,
                'pending_count' => (int) $row->pending_count,
                'total_quantity'=> (int) $row->total_quantity,
                // heat intensity: critical=1.0, high=0.7, medium=0.4
                'intensity'     => $row->priority_level === 'critical' ? 1.0 : ($row->priority_level === 'high' ? 0.7 : 0.4),
            ]);
        return response()->json($data);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\TwinFamily;
use App\Models\TwinSupporter;
use App\Models\TwinMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HumanTwinController extends Controller
{
    /* ─── Families ─────────────────────────────────────────────── */

    /** GET /twin/families — list pending/active families (public) */
    public function families(Request $request)
    {
        $q = TwinFamily::with('supporter:id,name,city');

        if ($request->has('city')) {
            $q->where('city', $request->city);
        }
        if ($request->has('status')) {
            $q->where('status', $request->status);
        }

        return response()->json($q->latest()->paginate(12));
    }

    /** POST /twin/families — family registers for support */
    public function registerFamily(Request $request)
    {
        $data = $request->validate([
            'family_head_name' => 'required|string|max:100',
            'phone'            => 'required|string|max:20|unique:twin_families,phone',
            'city'             => 'required|string|max:60',
            'area'             => 'nullable|string|max:60',
            'members_count'    => 'required|integer|min:1|max:30',
            'needs'            => 'required|array|min:1',
            'needs.*'          => 'string|in:food,education,medical,psychological,housing,financial',
            'story'            => 'nullable|string|max:1000',
        ]);

        $family = TwinFamily::create($data);

        return response()->json([
            'message' => 'تم تسجيل طلب الأسرة، سيتم التواصل معك خلال 48 ساعة',
            'id'      => $family->id,
        ], 201);
    }

    /* ─── Supporters ────────────────────────────────────────────── */

    /** GET /twin/supporters — list active supporters (public) */
    public function supporters(Request $request)
    {
        $q = TwinSupporter::with('family:id,family_head_name,city,status')
            ->where('status', '!=', 'paused');

        if ($request->has('city')) {
            $q->where('city', $request->city);
        }

        return response()->json($q->latest()->paginate(12));
    }

    /** POST /twin/supporters — supporter registers (public) */
    public function registerSupporter(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => 'required|string|max:20|unique:twin_supporters,phone',
            'email'          => 'nullable|email|max:120',
            'city'           => 'required|string|max:60',
            'support_types'  => 'required|array|min:1',
            'support_types.*'=> 'string|in:financial,consulting,emotional,educational,housing',
            'monthly_budget' => 'nullable|integer|min:0',
            'bio'            => 'nullable|string|max:500',
        ]);

        $supporter = TwinSupporter::create($data);

        // Auto-match: find first pending family in same city
        $family = TwinFamily::where('status', 'pending')
            ->where('city', $data['city'])
            ->whereNull('matched_supporter_id')
            ->first();

        if ($family) {
            $supporter->update(['status' => 'matched', 'matched_family_id' => $family->id]);
            $family->update(['status' => 'matched', 'matched_supporter_id' => $supporter->id]);

            // Platform welcome message
            TwinMessage::create([
                'supporter_id' => $supporter->id,
                'family_id'    => $family->id,
                'sender_type'  => 'platform',
                'message'      => "تهانينا! تم ربط الداعم {$supporter->name} مع أسرة {$family->family_head_name}. يمكنكم الآن التواصل والبدء في رحلة الدعم الإنساني.",
            ]);

            return response()->json([
                'message'     => 'تم التسجيل وتم إيجاد أسرة مناسبة لك في مدينتك!',
                'id'          => $supporter->id,
                'matched_to'  => $family->family_head_name,
                'family_city' => $family->city,
            ], 201);
        }

        return response()->json([
            'message' => 'تم التسجيل! سيتم إخطارك عند العثور على أسرة مناسبة',
            'id'      => $supporter->id,
        ], 201);
    }

    /* ─── Messages ──────────────────────────────────────────────── */

    /** GET /twin/messages/{pair_id} — fetch messages for a pair */
    public function messages(Request $request, $supporterId, $familyId)
    {
        $msgs = TwinMessage::where('supporter_id', $supporterId)
            ->where('family_id', $familyId)
            ->orderBy('created_at')
            ->get();

        return response()->json($msgs);
    }

    /** POST /twin/messages — send a message */
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'supporter_id' => 'required|integer|exists:twin_supporters,id',
            'family_id'    => 'required|integer|exists:twin_families,id',
            'sender_type'  => 'required|in:supporter,family,platform',
            'message'      => 'required|string|max:2000',
        ]);

        $msg = TwinMessage::create($data);

        return response()->json(['message' => 'تم إرسال الرسالة', 'id' => $msg->id], 201);
    }

    /* ─── Stats ─────────────────────────────────────────────────── */

    /** GET /twin/stats — dashboard stats */
    public function stats()
    {
        return response()->json([
            'total_families'    => TwinFamily::count(),
            'pending_families'  => TwinFamily::where('status', 'pending')->count(),
            'matched_pairs'     => TwinFamily::where('status', 'matched')->count(),
            'total_supporters'  => TwinSupporter::count(),
            'total_messages'    => TwinMessage::count(),
        ]);
    }
}

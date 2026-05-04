<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class SmartInventoryController extends Controller
{
    public function index(Request $request)
    {
        $q = InventoryItem::query();
        if ($request->type)   $q->where('type', $request->type);
        if ($request->status) $q->where('status', $request->status);
        return response()->json($q->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'         => 'required|in:medicine,equipment,food',
            'title'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'quantity'     => 'required|integer|min:1',
            'condition'    => 'nullable|string|max:50',
            'expiry_date'  => 'nullable|date',
            'contact_info' => 'nullable|string|max:200',
            'area_id'      => 'nullable|integer',
            'image_url'    => 'nullable|url|max:500',
        ]);
        $data['status'] = 'available';
        if (auth('sanctum')->check()) {
            $data['donor_id'] = auth('sanctum')->id();
        }
        $item = InventoryItem::create($data);
        return response()->json(['message' => 'تم إضافة العنصر بنجاح', 'item' => $item], 201);
    }

    public function reserve(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        if ($item->status !== 'available') {
            return response()->json(['message' => 'هذا العنصر غير متاح'], 422);
        }
        $item->update(['status' => 'reserved']);
        return response()->json(['message' => 'تم الحجز بنجاح']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Need;
use Illuminate\Http\Request;

class NeedController extends Controller
{
    public function index()
    {
        return Need::with('area')->get();
    }

    public function show($id)
    {
        return Need::with('area')->findOrFail($id);
    }

    public function pending()
    {
        return Need::with('area')->where('status', 'pending')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id'  => 'required|exists:areas,id',
            'type'     => 'required|in:food,water,medicine,shelter,other',
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
            'lang'     => 'nullable|in:ar,en,fr',
        ]);

        $lang = $validated['lang'] ?? 'ar';

        $need = Need::create([
            'area_id'  => $validated['area_id'],
            'type'     => $validated['type'],
            'quantity' => $validated['quantity'],
            'notes'    => isset($validated['notes']) ? [$lang => $validated['notes']] : null,
            'status'   => 'pending',
        ]);

        return response()->json($need->load('area'), 201);
    }
}

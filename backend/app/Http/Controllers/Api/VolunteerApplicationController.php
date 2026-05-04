<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use Illuminate\Http\Request;

class VolunteerApplicationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'volunteer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'city' => 'required|string|max:120',
            'age' => 'nullable|integer|min:16|max:80',
            'specialties' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
        ]);

        $application = VolunteerApplication::create($data);

        return response()->json([
            'message' => 'تم إرسال طلب التطوع بنجاح',
            'id' => $application->id,
        ], 201);
    }

    public function stats()
    {
        return response()->json([
            'total' => VolunteerApplication::count(),
            'pending' => VolunteerApplication::where('status', 'pending')->count(),
            'approved' => VolunteerApplication::where('status', 'approved')->count(),
        ]);
    }
}
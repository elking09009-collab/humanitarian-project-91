<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CsrCompany;
use Illuminate\Http\Request;

class CsrCompanyController extends Controller
{
    public function index()
    {
        $companies = CsrCompany::where('status', 'active')
            ->orderByDesc('total_donated')
            ->get();
        return response()->json($companies);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:200',
            'contact_email'  => 'required|email|max:200',
            'contact_phone'  => 'nullable|string|max:20',
            'sector'         => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'website'        => 'nullable|url|max:300',
            'matching_ratio' => 'nullable|numeric|min:0.5|max:5',
        ]);
        $company = CsrCompany::create($data);
        return response()->json([
            'message' => 'تم تسجيل طلب شراكة الشركة — سيتم مراجعته والتواصل معكم',
            'company' => $company
        ], 201);
    }
}

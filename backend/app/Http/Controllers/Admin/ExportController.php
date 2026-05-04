<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Need;
use App\Models\AccountReviewLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function users(Request $request)
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $status = $request->query('status');
        $query = User::where('role', '!=', 'admin');
        if ($status) {
            $query->where('status', $status);
        }
        $users = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($users) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'الاسم', 'البريد الإلكتروني', 'الهاتف', 'الدور', 'الحالة', 'سبب الرفض', 'تاريخ الإنشاء']);
            foreach ($users as $user) {
                fputcsv($out, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? '',
                    match($user->role) {
                        'admin' => 'أدمن',
                        'volunteer' => 'متطوع',
                        'organization' => 'منظمة',
                        default => $user->role,
                    },
                    match($user->status) {
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        default => $user->status ?? '',
                    },
                    $user->rejection_reason ?? '',
                    $user->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function needs(Request $request)
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $needs = Need::with('area')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="needs_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($needs) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'المنطقة', 'نوع الاحتياج', 'الكمية', 'الحالة', 'ملاحظات', 'تاريخ الإنشاء']);
            foreach ($needs as $need) {
                fputcsv($out, [
                    $need->id,
                    $need->area?->getTranslation('name', 'ar', false)
                        ?? $need->area?->getTranslation('name', 'en', false)
                        ?? '',
                    match($need->type) {
                        'food' => 'طعام',
                        'water' => 'مياه',
                        'medicine' => 'دواء',
                        'shelter' => 'مأوى',
                        'other' => 'أخرى',
                        default => $need->type,
                    },
                    $need->quantity,
                    match($need->status) {
                        'pending' => 'معلقة',
                        'delivered' => 'تم إيصالها',
                        default => $need->status,
                    },
                    $need->getTranslation('notes', 'ar', false)
                        ?? $need->getTranslation('notes', 'en', false)
                        ?? '',
                    $need->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function auditLogs(Request $request)
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $logs = AccountReviewLog::with(['user', 'reviewer'])->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="audit_logs_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'المستخدم', 'المراجع', 'من حالة', 'إلى حالة', 'السبب', 'التاريخ']);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->id,
                    $log->user?->email ?? '',
                    $log->reviewer?->name ?? '',
                    $log->from_status ?? '',
                    $log->to_status ?? '',
                    $log->reason ?? '',
                    $log->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}

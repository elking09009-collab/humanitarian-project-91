<?php

namespace App\Console\Commands;

use App\Mail\WeeklyReportMail;
use App\Models\AccountReviewLog;
use App\Models\Need;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyReport extends Command
{
    protected $signature = 'report:weekly';

    protected $description = 'Send weekly humanitarian dashboard report to admin email';

    public function handle(): int
    {
        $weekStart = now()->subDays(7);

        $stats = [
            'from' => $weekStart,
            'to' => now(),
            'users_total' => User::count(),
            'users_new' => User::where('created_at', '>=', $weekStart)->count(),
            'users_pending' => User::where('status', 'pending')->count(),
            'needs_total' => Need::count(),
            'needs_new' => Need::where('created_at', '>=', $weekStart)->count(),
            'needs_pending' => Need::where('status', 'pending')->count(),
            'needs_delivered' => Need::where('status', 'delivered')->count(),
            'reviews_approved' => AccountReviewLog::where('to_status', 'approved')
                ->where('created_at', '>=', $weekStart)
                ->count(),
            'reviews_rejected' => AccountReviewLog::where('to_status', 'rejected')
                ->where('created_at', '>=', $weekStart)
                ->count(),
            'needs_by_type' => Need::selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->get(),
        ];

        $recipient = env('WEEKLY_REPORT_EMAIL')
            ?: User::where('role', 'admin')->value('email')
            ?: config('mail.from.address');

        if (! $recipient) {
            $this->error('No recipient email found. Set WEEKLY_REPORT_EMAIL or ensure an admin user exists.');
            return self::FAILURE;
        }

        $pdfBinary = null;

        try {
            $pdfBinary = Pdf::loadView('pdf.weekly-report', ['stats' => $stats])->output();
        } catch (\Throwable $e) {
            $this->warn('PDF generation failed, sending email without attachment: ' . $e->getMessage());
        }

        Mail::to($recipient)->send(new WeeklyReportMail($stats, $pdfBinary));

        $this->info('Weekly report sent to: ' . $recipient);

        return self::SUCCESS;
    }
}

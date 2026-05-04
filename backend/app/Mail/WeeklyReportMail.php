<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $stats,
        public ?string $pdfBinary = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'التقرير الأسبوعي - منصة الدعم الإنساني',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.weekly-report',
            with: ['stats' => $this->stats],
        );
    }

    public function attachments(): array
    {
        if (! $this->pdfBinary) {
            return [];
        }

        return [
            Attachment::fromData(fn () => $this->pdfBinary, 'weekly-report-' . now()->format('Y-m-d') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}

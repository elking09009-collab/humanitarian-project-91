<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewAccountPendingNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $applicant) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => 'طلب حساب جديد',
            'body'    => "تلقينا طلب تسجيل جديد من: {$this->applicant->email} ({$this->applicant->name})",
            'url'     => '/admin/users',
            'user_id' => $this->applicant->id,
        ];
    }
}

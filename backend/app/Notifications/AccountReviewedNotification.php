<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $action,   // 'approved' | 'rejected'
        protected ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->action === 'approved') {
            return [
                'title' => 'تم قبول حسابك',
                'body'  => 'تهانينا! تمت الموافقة على حساب المستخدم الخاص بك.',
                'url'   => '/',
            ];
        }

        return [
            'title'  => 'تم رفض طلب حسابك',
            'body'   => 'عذراً، تم رفض طلب التسجيل.' . ($this->reason ? ' السبب: ' . $this->reason : ''),
            'url'    => '/',
        ];
    }
}

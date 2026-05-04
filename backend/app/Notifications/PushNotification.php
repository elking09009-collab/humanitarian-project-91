<?php

namespace App\Notifications;

use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * إشعار Push عبر Firebase FCM
 *
 * الاستخدام:
 *   $user->notify(new PushNotification('عنوان', 'نص الرسالة', ['url' => '/admin/users']));
 *
 * شرط: يجب أن يكون لدى $user عمود fcm_token
 */
class PushNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
        protected array  $data = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];   // يُخزن أيضاً في DB للرجوع إليه
    }

    public function toDatabase(object $notifiable): array
    {
        // إرسال FCM إن وُجد token
        if (! empty($notifiable->fcm_token)) {
            app(FcmService::class)->sendToToken(
                $notifiable->fcm_token,
                $this->title,
                $this->body,
                $this->data
            );
        }

        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
        ];
    }
}

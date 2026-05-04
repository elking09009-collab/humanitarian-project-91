<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging HTTP v1 Service
 *
 * إعداد مطلوب في .env:
 *   FIREBASE_PROJECT_ID=your-project-id
 *   FIREBASE_SERVER_KEY=your-server-key-or-service-account-token
 */
class FcmService
{
    private string $projectId;
    private string $serverKey;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id', '');
        $this->serverKey = config('services.firebase.server_key', '');
    }

    private function useLegacyEndpoint(): bool
    {
        // Most projects still use server key format (AAAA...); this requires legacy FCM endpoint.
        return str_starts_with($this->serverKey, 'AAAA');
    }

    /**
     * إرسال إشعار Push لجهاز واحد عبر token
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (empty($this->projectId) || empty($this->serverKey)) {
            Log::warning('FCM: FIREBASE_PROJECT_ID أو FIREBASE_SERVER_KEY غير مضبوط في .env');
            return false;
        }

        try {
            if ($this->useLegacyEndpoint()) {
                $payload = [
                    'to'           => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data'         => array_map('strval', $data),
                    'priority'     => 'high',
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(10)
                    ->post('https://fcm.googleapis.com/fcm/send', $payload);
            } else {
                $payload = [
                    'message' => [
                        'token'        => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data'         => array_map('strval', $data),
                        'android'      => ['priority' => 'high'],
                        'apns'         => ['headers' => ['apns-priority' => '10']],
                    ],
                ];

                $endpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
                $response = Http::withToken($this->serverKey)
                    ->timeout(10)
                    ->post($endpoint, $payload);
            }

            if ($response->successful()) {
                Log::info('FCM: إشعار أُرسل بنجاح', ['token' => substr($token, 0, 20) . '...']);
                return true;
            }

            Log::error('FCM: فشل الإرسال', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('FCM: استثناء أثناء الإرسال', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * إرسال إشعار Push لمجموعة tokens (Multicast)
     */
    public function sendToMultiple(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = ['success' => 0, 'failure' => 0];
        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data)
                ? $results['success']++
                : $results['failure']++;
        }
        return $results;
    }

    /**
     * إرسال إشعار لموضوع (Topic) مثل "admins" أو "volunteers"
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        if (empty($this->projectId) || empty($this->serverKey)) {
            return false;
        }

        try {
            if ($this->useLegacyEndpoint()) {
                $payload = [
                    'to'           => '/topics/' . ltrim($topic, '/'),
                    'notification' => ['title' => $title, 'body' => $body],
                    'data'         => array_map('strval', $data),
                    'priority'     => 'high',
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(10)
                    ->post('https://fcm.googleapis.com/fcm/send', $payload);
            } else {
                $payload = [
                    'message' => [
                        'topic'        => $topic,
                        'notification' => ['title' => $title, 'body' => $body],
                        'data'         => array_map('strval', $data),
                    ],
                ];

                $endpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
                $response = Http::withToken($this->serverKey)
                    ->timeout(10)
                    ->post($endpoint, $payload);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('FCM Topic: ' . $e->getMessage());
            return false;
        }
    }
}

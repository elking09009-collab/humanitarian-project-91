<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OchaApiService
{
    public function latestReports(int $limit = 10): array
    {
        $response = Http::timeout(15)->get('https://api.reliefweb.int/v1/reports', [
            'appname' => 'humanitarian-tracking',
            'limit' => $limit,
            'sort[]' => 'date:desc',
            'fields[include][]' => ['title', 'source.name', 'url', 'date.created', 'country.name'],
        ]);

        if (! $response->successful()) {
            return [
                'success' => false,
                'status' => $response->status(),
                'data' => [],
            ];
        }

        $items = collect($response->json('data', []))
            ->map(function ($row) {
                $fields = $row['fields'] ?? [];

                return [
                    'title' => $fields['title'] ?? null,
                    'url' => $fields['url'] ?? null,
                    'published_at' => $fields['date']['created'] ?? null,
                    'source' => $fields['source'][0]['name'] ?? null,
                    'country' => $fields['country'][0]['name'] ?? null,
                ];
            })
            ->values()
            ->all();

        return [
            'success' => true,
            'status' => 200,
            'data' => $items,
        ];
    }
}

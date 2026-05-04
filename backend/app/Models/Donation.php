<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_id',
        'amount',
        'cause',
        'prev_hash',
        'hash',
    ];

    protected static function booted(): void
    {
        static::creating(function (Donation $donation) {
            $previous = self::query()->latest('id')->first();
            $donation->prev_hash = $previous?->hash;

            $payload = implode('|', [
                $donation->prev_hash ?? 'GENESIS',
                $donation->donor_id ?? 'anonymous',
                number_format((float) $donation->amount, 2, '.', ''),
                $donation->cause ?? '',
                now()->toIso8601String(),
            ]);

            $donation->hash = hash('sha256', $payload);
        });
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }
}

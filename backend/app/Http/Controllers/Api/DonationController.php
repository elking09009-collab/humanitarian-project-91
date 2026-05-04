<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;

class DonationController extends Controller
{
    public function verify(int $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);

        $prev = Donation::query()->where('id', '<', $donation->id)->latest('id')->first();
        $expectedPrevHash = $prev?->hash;

        $chainValid = $donation->prev_hash === $expectedPrevHash;

        return response()->json([
            'success' => true,
            'donation_id' => $donation->id,
            'current_hash' => $donation->hash,
            'previous_hash' => $donation->prev_hash,
            'expected_previous_hash' => $expectedPrevHash,
            'chain_valid' => $chainValid,
        ]);
    }
}

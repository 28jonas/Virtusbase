<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bankingcard;
use App\Models\DailyBalanceUpdate;
use App\Models\Card;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Support\Facades\Log;

class ChartController extends BaseApiController
{
    public function balanceHistory(Request $request)
    {
        Log::info('👉 /api/chart/balance-history route reached on login backend');
        Log::info('Request data: ' . json_encode($request->all()));
        $request->validate([
            'card_id' => 'required|exists:bankingcards,id',
            'range' => 'sometimes|in:7d,14d,30d,60d,90d,1y,all'
        ]);

        $card = Bankingcard::find($request->card_id);

        // Check if user owns the card
        if ($card->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $days = $this->getDaysFromRange($request->range);

        $query = DailyBalanceUpdate::where('bankingcard_id', $request->card_id)
            ->orderBy('snapshot_date');

        if ($days !== 'all') {
            $query->where('snapshot_date', '>=', Carbon::now()->subDays($days));
        }

        $snapshots = $query->get(['snapshot_date', 'balance']);

        return response()->json($snapshots);
    }

    private function getDaysFromRange($range)
    {
        return match($range) {
            '7d' => 7,
            '14d' => 14,
            '30d' => 30,
            '60d' => 60,
            '90d' => 90,
            '1y' => 365,
            'all' => 'all',
            default => 30
        };
    }
}
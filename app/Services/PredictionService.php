<?php

namespace App\Services;

use App\Models\Item;
use Carbon\Carbon;

class PredictionService
{
    /**
     * Predict how many days of stock are left for an item.
     * Returns null if no 'out' transactions found.
     */
    public function predictDaysLeft(Item $item): ?int
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Calculate total quantity out in the last 30 days
        $totalOut = $item->transactions()
            ->where('type', 'out')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('quantity');

        if ($totalOut <= 0) {
            return null;
        }

        $dailyBurnRate = $totalOut / 30;
        
        if ($dailyBurnRate <= 0) {
            return null;
        }

        return (int) floor($item->stok_barang / $dailyBurnRate);
    }
}

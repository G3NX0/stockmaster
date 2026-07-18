<?php
use App\Models\Item;
use App\Models\Transaction;
use App\Services\PredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('prediction service calculates days left correctly', function () {
    $item = Item::factory()->create(['stok_barang' => 100]);
    
    // Create 30 'out' transactions of 1 unit each in the last 30 days
    for ($i = 0; $i < 30; $i++) {
        Transaction::create([
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 1,
            'created_at' => now()->subDays($i)
        ]);
    }

    $service = new PredictionService();
    $daysLeft = $service->predictDaysLeft($item);

    // Burn rate = 30 / 30 = 1 per day
    // 100 stock / 1 per day = 100 days
    expect($daysLeft)->toBe(100);
});

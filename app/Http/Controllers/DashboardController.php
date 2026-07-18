<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = \App\Models\Item::count();
        $totalStock = \App\Models\Item::sum('stok_barang');
        $totalValue = \App\Models\Item::sum(DB::raw('COALESCE(selling_price, harga_barang) * stok_barang'));
        $lowStockCount = \App\Models\Item::where('stok_barang', '<', 10)->count();

        $recentTransactions = \App\Models\Transaction::with('item')->latest('id')->take(6)->get();

        // Pre-map for Alpine.js initial state (avoids complex closures inside @json in Blade)
        $recentTransactionsFeed = $recentTransactions->map(function ($tx) {
            return [
                'id'        => $tx->id,
                'type'      => $tx->type,
                'quantity'  => $tx->quantity,
                'item_name' => $tx->item ? $tx->item->nama_barang : 'Unknown',
                'time'      => $tx->created_at->diffForHumans(),
            ];
        })->values();
        $latestTransactionId = \App\Models\Transaction::max('id') ?? 0;

        $lowStockItems = \App\Models\Item::where('stok_barang', '<', 10)->take(5)->get();

        // Calculate dynamic category allocations
        $categoryAllocations = \App\Models\Category::withSum('items', 'stok_barang')
            ->get()
            ->filter(fn($cat) => ($cat->items_sum_stok_barang ?? 0) > 0)
            ->map(fn($cat) => [
                'name' => $cat->name,
                'stock' => (int) $cat->items_sum_stok_barang
            ])
            ->values();

        $topPeak = $categoryAllocations->sortByDesc('stock')->first()['name'] ?? 'N/A';
        $efficiency = $totalItems > 0 ? number_format(100 - ($lowStockCount / $totalItems * 100), 1) . '%' : '100%';

        // Data for Chart.js (Transactions last 7 days)
        $last7Days = collect(range(0, 6))->map(function($i) {
            $date = now()->subDays($i)->format('Y-m-d');
            return [
                'date' => now()->subDays($i)->format('d M'),
                'in'   => \App\Models\Transaction::where('type', 'in')->whereDate('created_at', $date)->sum('quantity'),
                'out'  => \App\Models\Transaction::where('type', 'out')->whereDate('created_at', $date)->sum('quantity'),
            ];
        })->reverse()->values();

        // New Dynamic Data for Brutalist Dashboard
        $forecastData = array_values($last7Days->pluck('out')->toArray());
        
        $historicalTrendData = collect(range(0, 4))->map(function($i) {
            $dateStart = now()->subMonths(4 - $i)->startOfMonth();
            $dateEnd = now()->subMonths(4 - $i)->endOfMonth();
            return (int) \App\Models\Transaction::where('type', 'out')
                    ->whereBetween('created_at', [$dateStart, $dateEnd])->sum('quantity');
        })->toArray();
        $historicalTrendLabels = collect(range(0, 4))->map(function($i) {
            return now()->subMonths(4 - $i)->format('M');
        })->toArray();

        // AI Predictions Logic
        $aiPredictions = [];
        $overstockCat = \App\Models\Category::withSum('items', 'stok_barang')->orderByDesc('items_sum_stok_barang')->first();
        if ($overstockCat && $overstockCat->items_sum_stok_barang > 100) {
            $prob = min(99.9, 50 + ($overstockCat->items_sum_stok_barang / 10));
            $aiPredictions[] = [
                'text' => "Over-stock risk detected in category {$overstockCat->name}",
                'value' => number_format($prob, 1) . '%',
                'color' => 'amber'
            ];
        } else {
             $aiPredictions[] = [
                'text' => "No critical over-stock risks detected",
                'value' => '10.0%',
                'color' => 'emerald'
            ];
        }

        $reorderSupplier = \App\Models\Supplier::withCount(['items as low_stock_count' => function ($query) {
            $query->where('stok_barang', '<', 10);
        }])->having('low_stock_count', '>', 0)->orderByDesc('low_stock_count')->first();

        if ($reorderSupplier) {
            $prob = min(99.9, 60 + ($reorderSupplier->low_stock_count * 5));
            $aiPredictions[] = [
                'text' => "Reorder probability for supplier {$reorderSupplier->name}",
                'value' => number_format($prob, 1) . '%',
                'color' => 'emerald'
            ];
        } else {
            $aiPredictions[] = [
                'text' => "Inventory levels healthy across all suppliers",
                'value' => '95.0%',
                'color' => 'emerald'
            ];
        }

        // Global Stock Heatmap (Top 24 items)
        $topItems = \App\Models\Item::latest('updated_at')->take(24)->get();
        $heatmapData = $topItems->map(function($item) {
            $level = 0;
            if ($item->stok_barang > 50) $level = 4;
            elseif ($item->stok_barang > 30) $level = 3;
            elseif ($item->stok_barang > 15) $level = 2;
            elseif ($item->stok_barang > 5) $level = 1;
            return [
                'id' => $item->id,
                'name' => $item->nama_barang,
                'level' => $level,
                'stock' => $item->stok_barang
            ];
        })->toArray();
        while(count($heatmapData) < 24) {
            $heatmapData[] = ['id' => 0, 'name' => 'N/A', 'level' => 0, 'stock' => 0];
        }

        $histTrendLabelStr = collect($historicalTrendLabels)->implode(' - ');

        return view('dashboard', compact(
            'totalItems', 'totalStock', 'totalValue', 'lowStockCount',
            'recentTransactions', 'recentTransactionsFeed', 'latestTransactionId',
            'last7Days', 'lowStockItems',
            'categoryAllocations', 'topPeak', 'efficiency',
            'forecastData', 'historicalTrendData', 'historicalTrendLabels',
            'aiPredictions', 'heatmapData', 'histTrendLabelStr'
        ));

    }

    public function scanner()
    {
        return view('scanner');
    }

    // ── Real-time polling endpoint for dashboard ─────────────────────────────
    public function poll(Request $request)
    {
        $lastId = (int) $request->query('last_id', 0);

        $newTransactions = \App\Models\Transaction::with('item')
            ->where('id', '>', $lastId)
            ->latest('id')
            ->take(10)
            ->get()
            ->map(fn($tx) => [
                'id'        => $tx->id,
                'type'      => $tx->type,
                'quantity'  => $tx->quantity,
                'item_name' => $tx->item ? $tx->item->nama_barang : 'Unknown',
                'time'      => $tx->created_at->diffForHumans(),
            ]);

        $stats = [
            'totalItems'   => \App\Models\Item::count(),
            'totalStock'   => \App\Models\Item::sum('stok_barang'),
            'totalValue'   => \App\Models\Item::sum(DB::raw('COALESCE(selling_price, harga_barang) * stok_barang')),
            'lowStockCount'=> \App\Models\Item::where('stok_barang', '<', 10)->count(),
        ];

        return response()->json([
            'transactions' => $newTransactions,
            'stats'        => $stats,
            'latest_id'    => $newTransactions->isNotEmpty() ? $newTransactions->max('id') : $lastId,
        ]);
    }

    public function sendWhatsappReport(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya Admin yang dapat mengirim laporan ke WhatsApp.'], 403);
        }

        try {
            $reportType = $request->input('report_type', 'daily');
            $botUrl = env('WHATSAPP_BOT_URL', 'http://localhost:5000');
            
            // Map report types to bot endpoints
            $endpoints = [
                'daily'          => '/report/daily',
                'weekly'         => '/report/weekly',
                'monthly'        => '/report/monthly',
                'stock-critical' => '/report/stock-critical',
                'summary'        => '/report/summary',
            ];

            $endpoint = $endpoints[$reportType] ?? '/report/daily';

            $response = Http::timeout(5)->post($botUrl . $endpoint, [
                'title' => 'LAPORAN MANUAL ADMIN',
            ]);

            if ($response->successful() && $response->json('success') === true) {
                return response()->json(['success' => true, 'message' => 'Laporan berhasil dikirim ke WhatsApp Owner!']);
            }

            $errorMsg = $response->json('message') ?? 'Gagal mengirim laporan. Pastikan WhatsApp Bot terhubung.';
            return response()->json(['success' => false, 'message' => $errorMsg]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Koneksi ke WhatsApp Bot gagal: ' . $e->getMessage()]);
        }
    }
}

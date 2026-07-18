<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Tren Transaksi (7 Hari Terakhir)
        $days = collect(range(6, 0))->map(function ($i) {
            return Carbon::today()->subDays($i)->format('Y-m-d');
        });

        $trends = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in'),
            DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out')
        )
        ->where('created_at', '>=', Carbon::today()->subDays(6))
        ->groupBy('date')
        ->get()
        ->keyBy('date');

        $chartData = [
            'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M')),
            'in' => $days->map(fn($d) => $trends->get($d)->total_in ?? 0),
            'out' => $days->map(fn($d) => $trends->get($d)->total_out ?? 0),
        ];

        // 2. Distribusi Kategori
        $categories = Category::withCount('items')->get();
        $categoryData = [
            'labels' => $categories->pluck('name'),
            'counts' => $categories->pluck('items_count'),
        ];

        // 3. Stock Turnover Ratio (Simplified: Out / Avg Stock in 30 days)
        $totalMonthlyOut = Transaction::where('type', 'out')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('quantity');
        
        $currentStock = Item::sum('stok_barang');
        $turnoverRatio = ($currentStock + $totalMonthlyOut) > 0 
            ? round(($totalMonthlyOut / ($currentStock + $totalMonthlyOut / 2)) * 100, 2) 
            : 0;

        // 4. Barang Paling Sering Keluar (Top 5)
        $topItems = Transaction::select('item_id', DB::raw('SUM(quantity) as total'))
            ->where('type', 'out')
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('reports.index', compact('chartData', 'categoryData', 'turnoverRatio', 'topItems', 'totalMonthlyOut'));
    }
    
    public function forecasting(\App\Services\PredictionService $predictionService)
    {
        $items = Item::with(['category', 'unit'])->get();
        
        $items->each(function($item) use ($predictionService) {
            $item->days_left = $predictionService->predictDaysLeft($item);
            
            // Calculate health status
            if ($item->days_left === null) {
                $item->status = 'Safe (No Movement)';
                $item->status_color = 'slate';
            } elseif ($item->days_left <= 3) {
                $item->status = 'CRITICAL';
                $item->status_color = 'red';
            } elseif ($item->days_left <= 7) {
                $item->status = 'WARNING';
                $item->status_color = 'amber';
            } else {
                $item->status = 'SAFE';
                $item->status_color = 'emerald';
            }
        });

        // Sort items: critical first
        $items = $items->sortBy(function($item) {
            return $item->days_left ?? 999;
        });

        return view('reports.forecasting', compact('items'));
    }

    public function heatmap()
    {
        $categories = Category::with('items.unit')->get();
        
        $heatmapData = $categories->map(function($category) {
            return [
                'name' => $category->name,
                'items' => $category->items->map(function($item) {
                    return [
                        'name' => $item->nama_barang,
                        'value' => $item->stok_barang * $item->harga_barang,
                        'stock' => $item->stok_barang,
                        'unit' => $item->unit->name ?? 'Unit'
                    ];
                })
            ];
        });

        return view('reports.heatmap', compact('heatmapData'));
    }

    public function expiringItems()
    {
        $batches = \App\Models\Batch::with('item.unit')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->orderBy('expiry_date')
            ->get();

        return view('reports.expiring', compact('batches'));
    }

    public function profitMargin()
    {
        $items = Item::whereNotNull('selling_price')
            ->where('selling_price', '>', 0)
            ->with('category')
            ->get();

        return view('reports.profit', compact('items'));
    }
}

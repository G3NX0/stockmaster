<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Category;
use App\Services\AiService;
use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request)
    {
        $query = strtolower($request->input('message'));
        
        // Attempt LLM Response
        $llmResponse = $this->aiService->ask($query);
        
        if ($llmResponse === "NODES_OFFLINE") {
            $response = $this->getInternalResponse($query);
        } else {
            $response = $llmResponse;
        }

        return response()->json([
            'message' => $response,
            'timestamp' => now()->format('H:i'),
            'status' => 'synapse_active'
        ]);
    }

    protected function getInternalResponse($query)
    {
        // Sophisticated Fallback Logic (The 'Invisible' Assistant)
        if (str_contains($query, 'stok') || str_contains($query, 'stock') || str_contains($query, 'aman')) {
            $lowStockCount = Item::whereRaw('stok_barang <= min_stock')->count();
            $totalStock = Item::sum('stok_barang');
            
            if ($lowStockCount > 0) {
                return "Saat ini saya mendeteksi ada {$lowStockCount} item yang memerlukan perhatian segera karena stok di bawah batas minimum. Secara keseluruhan, Anda memiliki total {$totalStock} unit di inventaris. Apakah Anda ingin saya menampilkan daftar item yang kritis?";
            }
            return "Kabar baik! Semua unit inventaris Anda saat ini dalam kondisi aman dan optimal. Total volume fisik mencapai {$totalStock} unit. Ada hal lain yang bisa saya bantu analisis?";
        } 
        
        if (str_contains($query, 'nilai') || str_contains($query, 'harga') || str_contains($query, 'value') || str_contains($query, 'duit')) {
            $totalValue = Item::select(DB::raw('SUM(harga_barang * stok_barang) as total'))->first()->total;
            $formattedValue = number_format($totalValue, 0, ',', '.');
            return "Berdasarkan audit data terbaru, total nilai aset yang tersimpan di gudang Anda mencapai Rp {$formattedValue}. Ini adalah angka yang sangat solid untuk mendukung operasional bisnis Anda.";
        }

        if (str_contains($query, 'halo') || str_contains($query, 'hi') || str_contains($query, 'pagi') || str_contains($query, 'siang') || str_contains($query, 'malam')) {
            return "Halo! Saya Synaptic AI, asisten intelijen Anda. Saya siap membantu Anda menganalisis data stok, memantau valuasi aset, atau memberikan wawasan tentang operasional gudang hari ini. Apa yang ingin Anda diskusikan?";
        }

        return "Maaf, saya sedang mensinkronisasi data yang Anda minta. Namun secara umum, sistem StockMaster berjalan sangat optimal hari ini. Anda bisa bertanya spesifik tentang level stok atau nilai aset kepada saya.";
    }
}

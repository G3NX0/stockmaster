<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected $nodes = ['gemini_1', 'gemini_2'];
    
    protected function getSystemContext()
    {
        $totalItems = Item::count();
        $totalStock = Item::sum('stok_barang');
        $totalValue = Item::select(DB::raw('SUM(harga_barang * stok_barang) as total'))->first()->total;
        $lowStockItems = Item::whereRaw('stok_barang <= min_stock')->pluck('nama_barang')->take(5)->toArray();
        $lowStockText = empty($lowStockItems) ? 'semua stok dalam kondisi aman' : implode(', ', $lowStockItems);
        
        return "You are 'Synaptic', a high-fidelity AI Warehouse Assistant for the StockMaster Enterprise system.
        
        STRICT RULES:
        - Be concise and to-the-point. Avoid long introductory sentences.
        - Answer directly. If the user asks for status, give numbers and facts immediately.
        - Maintain a professional, warm, yet very efficient persona.
        - Use simple formatting (bullet points) for data.
        - If stock is critical, mention it briefly.
        
        Current Inventory Data:
        - Total jenis barang: {$totalItems}
        - Total stok fisik: " . number_format($totalStock) . " unit
        - Total nilai aset: Rp " . number_format($totalValue, 0, ',', '.') . "
        - Status stok kritis: " . ($lowStockText === 'semua stok dalam kondisi aman' ? "Aman" : $lowStockText) . "
        
        Tugasmu: Bantu user mengelola gudang dengan jawaban yang solutif, manusiawi, dan berwawasan luas. Jika user menyapa, balaslah dengan hangat!";
    }

    public function ask($userMessage)
    {
        $context = $this->getSystemContext();
        
        foreach ($this->nodes as $node) {
            $response = $this->attemptGeminiRequest($node, $context, $userMessage);
            if ($response) return $response;
        }

        return "NODES_OFFLINE";
    }

    protected function attemptGeminiRequest($node, $context, $message)
    {
        $apiKey = config("services.ai.{$node}");
        if (!$apiKey) return null;

        // Debug: Log key prefix
        Log::debug("Attempting Synaptic Node {$node} with key: " . substr($apiKey, 0, 5) . "...");

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying() 
                ->timeout(30)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => "{$context}\n\nUser Question: {$message}"]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 1.0,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ]
                ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }
            
            // Critical Debug: Log the exact reason for failure
            Log::error("Synaptic Node {$node} Failed. Status: " . $response->status());
            Log::error("Error Detail: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Synaptic Node {$node} Exception: " . $e->getMessage());
        }

        return null;
    }
}

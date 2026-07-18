<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\AiService;
use Illuminate\Support\Facades\Http;

class AiServiceTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_ai_service_can_connect_to_google_gemini()
    {
        // Setup dummy data for context
        $cat = \App\Models\Category::create(['name' => 'Test Category']);
        \App\Models\Item::create([
            'nama_barang' => 'Test Item',
            'kode_barang' => 'TS-001',
            'category_id' => $cat->id,
            'stok_barang' => 10,
            'min_stock' => 5,
            'harga_barang' => 1000,
        ]);

        $service = new AiService();
        $response = $service->ask("Halo, siapa namamu?");
        
        $this->assertNotEquals("NODES_OFFLINE", $response, "AI Service is returning NODES_OFFLINE. Check logs for details.");
        $this->assertIsString($response);
        
        dump("AI Response: " . $response);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
    }

    public function test_can_export_items_to_excel()
    {
        $response = $this->actingAs($this->user)->get(route('items.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=inventory-report.xlsx');
    }

    public function test_can_import_items_from_excel()
    {
        $content = "SKU,Nama Barang,Harga,Stok,Kategori,Satuan,Supplier\nTEST-IMPORT,Imported Item,1000,50,Lainnya,Pcs,Internal";
        $file = UploadedFile::fake()->createWithContent('items.csv', $content);
        
        $response = $this->actingAs($this->user)->post(route('items.import-excel'), [
            'file' => $file
        ]);

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success', 'Data barang berhasil diimport.');
        $this->assertDatabaseHas('items', ['kode_barang' => 'TEST-IMPORT']);
    }

    public function test_import_fails_with_invalid_file()
    {
        $response = $this->actingAs($this->user)->post(route('items.import-excel'), [
            'file' => null
        ]);

        $response->assertSessionHasErrors('file');
    }
}

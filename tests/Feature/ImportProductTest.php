<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_excel_import_success(): void
    {
        Storage::disk('local')->put('import.xls', file_get_contents(storage_path('app/import_example.xls')));

        $file = new UploadedFile(
            storage_path('app/import.xls'),
            'import.xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->post('/products/import', [
            'file' => $file,
        ]);

        $response->assertSessionHas('success', true);

        $response->assertStatus(302);
    }
}

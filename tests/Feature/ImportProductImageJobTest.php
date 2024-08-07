<?php

namespace Tests\Feature;

use App\Jobs\ImportProductImageJob;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportProductImageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_product_image_job()
    {
        Storage::fake('public');
        Queue::fake();

        $product = Product::factory()->create();

        // Define the URL to the image
        $imageUrl = 'https://via.placeholder.com/150';

        // Dispatch the job
        ImportProductImageJob::dispatch($product, $imageUrl);

        Queue::assertPushed(ImportProductImageJob::class, function ($job) use ($product, $imageUrl) {
            return $job->product->id === $product->id && $job->url === $imageUrl;
        });

        // Run the job
        $job = new ImportProductImageJob($product, $imageUrl);
        $job->handle();

        echo json_encode(ProductImage::all()->toArray());

        $storedFiles = Storage::disk('public')->files("{$product->id}/");
        $this->assertNotEmpty($storedFiles, 'No files were stored in the directory.');

        $storedFile = basename($storedFiles[0]);

        Storage::disk('public')->assertExists("{$product->id}/{$storedFile}");

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'path' => "{$product->id}/{$storedFile}",
        ]);
    }
}

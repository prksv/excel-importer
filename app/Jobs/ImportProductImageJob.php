<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Str;
use URL;

class ImportProductImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product, public string $url)
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $contents = file_get_contents($this->url);

        if ($contents === false) {
            throw new \Exception("Unable to retrieve contents from URL: {$this->url}");
        }

        // Extract file extension from URL
        $urlParts = explode('.', $this->url);
        $extension = strtolower(end($urlParts)); // Get the last part after the last dot

        // List of allowed image extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // If the extension is not valid or missing, use MIME type detection
        if (!in_array($extension, $allowedExtensions) || !in_array($extension, $allowedExtensions)) {
            // Use finfo to detect MIME type from contents
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($contents);

            // Map MIME types to file extensions
            $mimeTypeToExtension = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
            ];

            $extension = $mimeTypeToExtension[$mimeType] ?? 'bin'; // Default to 'bin' if MIME type is unknown
        }

        // Generate a unique filename and path
        $filename = Str::uuid() . '.' . $extension;
        $path = "{$this->product->id}/{$filename}";

        // Save the file to storage
        Storage::disk('public')->put($path, $contents);

        // Associate the image with the product
        $this->product->images()->create(['path' => $path]);
    }
}

<?php

namespace App\Imports;

use App\Jobs\ImportProductImageJob;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use URL;

class ProductsImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        $headings = $rows->first()->flip();

        $additionalFields = $headings->filter(function ($value, $key) {
            return str_starts_with($key, 'Доп. поле: ');
        })->mapWithKeys(function ($value, $key) {
            return [str_replace('Доп. поле: ', '', $key) => $value];
        });

        foreach ($rows->skip(1) as $row) {
            $product = $this->createProduct($row, $headings);
            $this->importAdditionalFields($product, $additionalFields, $row);
        }
    }

    private function createProduct($row, $headings): Product
    {
        return Product::create([
            'name' => $row[$headings['Наименование']],
            'external_code' => $row[$headings['Внешний код']],
            'description' => $row[$headings['Описание']],
            'price' => (float) $row[$headings['Цена: Цена продажи']]
        ]);
    }

    private function isImageUrl(string $value): bool
    {
        return URL::isValidUrl($value) && preg_match('/\.(jpg|png)$/i', $value);
    }

//    private function saveProductImage(Product $product, string $url): void
//    {
//        try {
//            $contents = file_get_contents($url);
//            $extension = pathinfo($url, PATHINFO_EXTENSION);
//            $filename = uniqid() . '.' . $extension;
//            $path = "{$product->id}/{$filename}";
//
//            Storage::disk('public')->put($path, $contents);
//
//            $product->images()->create(['path' => $path]);
//        } catch (\Exception $e) {
//            \Log::error($e);
//        }
//    }

    private function importAdditionalFields(Product $product, Collection $additionalFields, Collection $row): void
    {
        foreach ($additionalFields as $additionalHeading => $key) {
            $value = $row[$key];

            if (is_null($value)) {
                continue;
            }

            $values = explode(', ', $value);

            foreach ($values as $value) {
                if ($this->isImageUrl($value)) {
                    ImportProductImageJob::dispatch($product, $value);
                } else {
                    $product->additionalFields()->create([
                        'key' => $additionalHeading,
                        'value' => $value
                    ]);
                }
            }
        }
    }
}

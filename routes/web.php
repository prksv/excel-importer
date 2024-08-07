<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('import', [ProductController::class, 'importForm']);
    Route::post('import', [ProductController::class, 'handleImport']);
    Route::get('{product}', [ProductController::class, 'show'])->name('product.show');
});

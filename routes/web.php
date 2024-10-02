<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::redirect('/', '/admin');

Route::get('/download/{id}', function ($id) {
    $file = App\Models\File::findOrFail($id);

    // Caminho do arquivo no storage
    $filePath = $file->file_path;

    // Gera a URL pública do arquivo
    $fileUrl = Storage::disk('spaces')->url($filePath);

    // Redireciona o usuário para a URL pública com o cabeçalho forçado
    return redirect($fileUrl)
        ->withHeaders([
            'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
        ]);
})->name('download.file');
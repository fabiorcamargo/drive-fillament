<?php

namespace App\Observers;

use App\Jobs\DeleteUploadFile;
use App\Jobs\UploadFileToSpace;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class SpaceObserver
{
    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {

    $file->update(['status' => 'subindo']);
        // Obter o caminho do arquivo armazenado
    $filePath = $file->file_path; // Caminho relativo do banco de dados, ex: 'uploads/nome_do_arquivo.ext'
    
    // Obter o caminho absoluto do arquivo local
    $fullFilePath = storage_path('app/public/' . $filePath);
    
    //dd($fullFilePath, basename($filePath), $file->id);
    // Verifique se o arquivo existe antes de despachar o job
    if (file_exists($fullFilePath)) {
        // Despacha o job para fazer o upload para o DigitalOcean Spaces
        UploadFileToSpace::dispatch($fullFilePath, basename($filePath), $file->id);
        
        // (Opcional) Para deletar o arquivo local após um certo tempo
        // DeleteUploadFile::dispatch($fullFilePath)->delay(now()->addMinutes(1));
       
    }

       
        // Obter o caminho absoluto do arquivo local
        //$fullFilePath = storage_path('app/public/' . $file->file_path);

        //DeleteUploadFile::dispatch($fullFilePath)->delay(60);
    }

    /**
     * Handle the File "updated" event.
     */
    public function updated(File $file): void
    {
        //
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        // Exclui o arquivo do Digital Ocean Spaces
        Storage::disk('spaces')->delete($file->file_path);
    }

    /**
     * Handle the File "restored" event.
     */
    public function restored(File $file): void
    {
        //
    }

    /**
     * Handle the File "force deleted" event.
     */
    public function forceDeleted(File $file): void
    {
        //
    }
}

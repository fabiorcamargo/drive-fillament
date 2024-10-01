<?php

namespace App\Observers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class SpaceObserver
{
    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {
        //
        //dd('s');
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

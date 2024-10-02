<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUploadFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fullFilePath;

    public function __construct($fullFilePath)
    {
        $this->fullFilePath = $fullFilePath;

        // Verificar se o arquivo local existe e removê-lo
        if (file_exists($fullFilePath)) {
            unlink($fullFilePath); // Remove o arquivo local
        }

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}

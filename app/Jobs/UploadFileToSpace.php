<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DeleteUploadFile;

use Exception; // Importar a classe Exception

class UploadFileToSpace implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileName;
    protected $fileId;
    public $timeout = 3000;

    public function __construct($filePath, $fileName, $fileId)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->fileId = $fileId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Mover o arquivo para o disk configurado
            Storage::disk('spaces')->putFileAs('uploads', $this->filePath, $this->fileName);

            DeleteUploadFile::dispatch($this->filePath);

            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }


            // Atualizar o progresso na base de dados
            $this->updateProgress('concluído'); // Defina 100% após o upload
        } catch (Exception $e) {
            // Captura o erro e atualiza o progresso na base de dados
            $this->updateProgress('erro'); // Pode ser 0% ou qualquer outro valor que faça sentido

        }
    }

    protected function updateProgress($status)
    {
        File::where('id', $this->fileId)->update(['status' => $status]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception)
    {
        // Atualizar o progresso na base de dados para indicar que o upload falhou
        $this->updateProgress('erro'); // Pode ser 0% ou qualquer outro valor que faça sentido

    }
}

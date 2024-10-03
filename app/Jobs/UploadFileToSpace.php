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
use App\Models\User;
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
            

            

            $file = File::find($this->fileId);
            // Atualizar o progresso na base de dados

            // Mover o arquivo para o disk configurado
            Storage::disk('spaces')->putFileAs('uploads', $this->filePath, $this->fileId);

            DeleteUploadFile::dispatch($this->filePath);
            
            $this->updateProgress('concluído'); // Defina 100% após o upload

            $users = User::all(['phone', 'name']);
            $nameFile = urlencode($this->fileName);
            $nameFile = str_replace('+', '%20', $nameFile);

            foreach($users as $user){
                //dd($user);
                SendMessage::dispatch('55' . $user->phone, 'Olá ' . $user->name . PHP_EOL . 'Novo arquivo disponível: ' . $file->name . PHP_EOL . PHP_EOL . 'Acesse através do link:' . PHP_EOL . env('DO_SPACES_URL') . '/uploads' . '/' . $this->fileId);
            }

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

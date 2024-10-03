<?php

namespace App\Filament\Resources\FileResource\Pages;

use App\Filament\Resources\FileResource;
use App\Jobs\SendMessage;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ListFiles extends ListRecords
{
    protected static string $resource = FileResource::class;

    public $defaultAction = 'openModalShow';

    public bool $showCodeModal = false; // Adicione esta propriedade

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function openModalShow(): Actions\Action
    {
        if (auth()->user()->phone == null || auth()->user()->updated_at < Carbon::now()->subMinute()) {
            return Actions\Action::make('openModal')
                ->visible(true)
                ->modalHeading('Insira seu número de telefone')
                ->modalDescription(new HtmlString("Por favor, insira seu número de telefone."))
                ->modalSubmitActionLabel('Salvar')
                ->color('success')
                ->modalCancelAction(false)
                ->modalCloseButton(false)
                ->closeModalByEscaping(false)
                ->closeModalByClickingAway(false)
                ->form([
                    TextInput::make('phone') // Campo para o número de telefone
                        ->label('Número de Telefone')
                        ->required() // Torna o campo obrigatório 
                        ->placeholder('Ex: 11999999999') // Placeholder 
                        ->rules('required|string|min:14|max:14') // Regras de validação 
                        ->prefix('55')
                        ->mask('99 9 9999 9999'), // Máscara para telefone com DDD 
                ])

                ->action(function (array $data): void {
                    // Salva o número e envia a mensagem
                    $phone = str_replace(' ', '', $data['phone']);
                    $code = rand(10000, 99999);

                    $this->savePhoneNumber($phone, $code);
                    SendMessage::dispatch('55' . $phone, 'Segue o código de verificação' . PHP_EOL . PHP_EOL . $code);

                    redirect('/admin/files');
                });
        } else {
            return Actions\Action::make('openCodeModal') // Renomeado para evitar confusão
                ->visible(auth()->user()->code !== 'success') // Usa o estado para controlar a visibilidade
                ->modalHeading('Insira o código de verificação') // Atualize o título
                ->modalDescription(new HtmlString("Por favor, insira o código enviado para seu telefone."))
                ->modalSubmitActionLabel('Verificar')
                ->color('success')
                ->modalCancelAction(false)
                ->modalCancelAction(false)
                ->modalCloseButton(false)
                ->closeModalByEscaping(false)
                ->closeModalByClickingAway(false)
                ->form([
                    TextInput::make('verification_code') // Campo para o código de verificação
                        ->label('Código de Verificação')
                        ->required() // Torna o campo obrigatório 
                        ->placeholder('Ex: 123456') // Placeholder 
                        ->rules('required|string|min:5|max:5'), // Adicione regras de validação 
                ])
                ->action(function (array $data): void {
                    // Aqui você pode implementar a lógica para verificar o código
                    if ($data['verification_code'] == auth()->user()->code) {
                        auth()->user()->update(['code' => 'success']);

                        Notification::make()
                            ->title('Código verificado com sucesso!')
                            ->success()
                            ->send();

                        SendMessage::dispatch('55' . auth()->user()->phone, 'Número ativado com sucesso!');
                    } else {
                        Notification::make()
                            ->title('Código de verificação inválido.')
                            ->danger()
                            ->send();
                    }

                    redirect('/admin/files'); // Redireciona após a verificação
                });
        }
    }

    // Lógica para salvar o número de telefone
    protected function savePhoneNumber($phone, $code)
    {
        auth()->user()->update(['phone' => $phone, 'code' => $code]); // Atualiza o telefone do usuário
    }
}

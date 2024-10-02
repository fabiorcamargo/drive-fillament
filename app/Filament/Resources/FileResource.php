<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Jobs\UploadFileToSpace;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Upload';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload de arquivos')
                    ->description('O Upload está limitado a 5GB, são aceitos imagens, vídeos e pdf.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            //->disk('spaces')
                            ->required(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Arquivo')
                            ->disk('public')
                            ->directory('uploads')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->rules('mimes:jpg,png,pdf,mp4,mov', 'max:20000000')
                            ->required(),
                    ])->columns(1)->aside()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->url(fn($record) => Storage::disk('spaces')->url($record->file_path), true), // Link clicável
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'erro' => 'hheroicon-o-x-circle',
                        'subindo' => 'heroicon-o-clock',
                        'concluído' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'erro' => 'danger',
                        'subindo' => 'warning',
                        'concluído' => 'success',
                        default => 'gray',
                    })
                    ->disabledClick(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->disabledClick(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->disabledClick(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->poll('5s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}

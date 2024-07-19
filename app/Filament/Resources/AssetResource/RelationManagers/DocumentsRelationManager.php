<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\FileUpload::make('path')
                    ->required()
                    ->maxSize('20480') // 20 MB
                    ->previewable()
                    ->directory('documents/assets')
                    ->storeFileNamesIn('filename')
                    ->moveFiles()
                    ->downloadable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('filename')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                // Tables\Columns\TextColumn::make('filename'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('md')
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $sortMax = $livewire->getOwnerRecord()->documents()?->max('sort') ?? 0;
                        $data['sort'] = $sortMax + 1;

                        return  $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn (Document $record) =>
                        Storage::disk('public')->url($record->path)
                    )
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->modalWidth('md')
                    ->modalHeading('Edit Document'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

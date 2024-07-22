<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use App\Models\Document;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->documents()?->count() ?? false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\FileUpload::make('path')
                    ->required()
                    ->hiddenOn('view')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize('20480') // 20 MB
                    ->previewable()
                    ->directory('documents/assets')
                    ->storeFileNamesIn('filename')
                    ->moveFiles()
                    ->downloadable(),

                PdfViewerField::make('path')
                    ->hiddenOn(['create', 'edit']),
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
                Tables\Actions\ViewAction::make()
                    ->modalWidth('3xl')
                    ->modalHeading('View Document'),
                Tables\Actions\EditAction::make()
                    ->modalWidth('3xl')
                    ->modalHeading('Edit Document'),
                Tables\Actions\DeleteAction::make()
                    ->modalWidth('3xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

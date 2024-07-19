<?php

namespace App\Filament\Resources\AssetResource\RelationManagers;

use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\FileUpload::make('path')
                    ->required()
                    ->image()
                    ->maxSize(2048)
                    ->directory('images/assets')
                    ->storeFileNamesIn('filename')
                    ->moveFiles(),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Asset Images';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->reorderable('sort')
            ->reorderRecordsTriggerAction(fn (Tables\Actions\Action $action, bool $isReordering) => $action
                ->button()
                ->color($isReordering ? 'success' : 'gray')
                ->label($isReordering ? 'Done' : 'Reorder')
            )
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->numeric(),
                Tables\Columns\ImageColumn::make('path')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('md')
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $sortMax = $livewire->getOwnerRecord()->images()?->max('sort') ?? 0;
                        $data['sort'] = $sortMax + 1;

                        return  $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('md')
                    ->modalHeading('Edit Image'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->inlineLabel()
            ->schema([
                Forms\Components\Section::make()
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('manufacturer_id')
                            ->relationship('manufacturer', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form) => ManufacturerResource::form($form))
                            ->createOptionModalHeading('Create Manufacturer')
                            ->editOptionForm(fn (Form $form) => ManufacturerResource::form($form))
                            ->editOptionModalHeading('Edit Manufacturer'),

                        Forms\Components\TextInput::make('model_number')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('serial_number')
                            ->maxLength(255),

                        Forms\Components\Select::make('location_id')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form) => LocationResource::form($form))
                            ->createOptionModalHeading('Create Location')
                            ->editOptionForm(fn (Form $form) => LocationResource::form($form))
                            ->editOptionModalHeading('Edit Location'),
                ]),
                Forms\Components\Section::make()
                    ->description('Purchase Info')
                    ->columns([
                        'default' => 1,
                        'lg' => 3,
                        // 'lg' => 5,
                    ])
                    ->schema([
                        Forms\Components\DatePicker::make('purchased_at')
                            ->label('Purchase Date'),

                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form) => VendorResource::form($form))
                            ->createOptionModalHeading('Create Vendor')
                            ->editOptionForm(fn (Form $form) => VendorResource::form($form))
                            ->editOptionModalHeading('Edit Vendor'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('images.path')
                    ->limit(1)
                    ->limitedRemainingText()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model_number')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Purchased On')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Price')
                    ->prefix('$')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('manufacturer.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view' => Pages\ViewAsset::route('/{record}'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}

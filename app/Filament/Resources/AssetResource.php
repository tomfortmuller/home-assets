<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use App\Models\Manufacturer;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use function Termwind\render;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getManufacturerOption(Model $model): string {
        // return "<div><b>$id</b> $name</div>";
        return view('filament-select-option')
            ->with('name', $model->name)
            ->with('logo', $model->logo);
    }

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

                        Forms\Components\Select::make('categories')
                            ->relationship(titleAttribute: 'name')
                            ->multiple()
                            ->preload(),

                        Forms\Components\Select::make('manufacturer_id')
                            ->relationship('manufacturer', 'name')
                            ->searchable()
                            ->allowHtml()
                            ->getSearchResultsUsing(function (string $search) {
                                $manufacturers = Manufacturer::where('name', 'like', "%$search%")->limit(50)->get();
                                return $manufacturers->mapWithKeys(function ($manufacturer) {
                                    return [$manufacturer->getKey() => static::getManufacturerOption($manufacturer)];
                                });
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $manufacturer = Manufacturer::find($value);
                                return static::getManufacturerOption($manufacturer);
                            })
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
                    ->inlineLabel()
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->allowHtml()
                            ->getSearchResultsUsing(function (string $search) {
                                $vendors = Vendor::where('name', 'like', "%$search%")->limit(50)->get();
                                return $vendors->mapWithKeys(function ($vendor) {
                                    return [$vendor->getKey() => static::getManufacturerOption($vendor)];
                                });
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $vendor = Vendor::find($value);
                                return static::getManufacturerOption($vendor);
                            })
                            ->createOptionForm(fn (Form $form) => VendorResource::form($form))
                            ->createOptionModalHeading('Create Vendor')
                            ->editOptionForm(fn (Form $form) => VendorResource::form($form))
                            ->editOptionModalHeading('Edit Vendor'),

                        Forms\Components\DatePicker::make('purchased_at')
                            ->label('Purchase Date'),

                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->prefix('$'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.path')
                    ->label('Image')
                    ->limit(1)
                    ->limitedRemainingText()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('categories.name')
                    ->badge()
                    ->separator(',')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('model_number')
                    ->searchable(isIndividual: true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Purchased On')
                    ->date()
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Price')
                    ->prefix('$')
                    ->numeric()
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('manufacturer.name')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->sortable()
                    ->searchable(isIndividual: true)
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
            RelationGroup::make('Related', [
                RelationManagers\ImagesRelationManager::class,
                RelationManagers\DocumentsRelationManager::class,
            ]),
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

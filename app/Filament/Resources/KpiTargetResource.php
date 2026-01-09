<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KpiTargetResource\Pages;
use App\Filament\Resources\KpiTargetResource\RelationManagers;
use App\Models\KpiTarget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KpiTargetResource extends Resource
{
    protected static ?string $model = KpiTarget::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Target KPI';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?string $pluralModelLabel = 'Target KPI';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Target KPI Bulanan')
                    ->description('Atur target KPI untuk perhitungan performa host')
                    ->schema([
                        Forms\Components\TextInput::make('gmv_per_hour')
                            ->label('GMV per Jam')
                            ->required()
                            ->numeric()
                            ->default(2700000)
                            ->prefix('Rp')
                            ->helperText('Target GMV per jam live'),
                        
                        Forms\Components\TextInput::make('conversion_rate')
                            ->label('Conversion Rate')
                            ->required()
                            ->numeric()
                            ->step(0.0001)
                            ->default(0.03)
                            ->suffix('%')
                            ->helperText('Target persentase konversi (0.03 = 3%)'),
                        
                        Forms\Components\TextInput::make('aov')
                            ->label('AOV (Average Order Value)')
                            ->required()
                            ->numeric()
                            ->default(180000)
                            ->prefix('Rp')
                            ->helperText('Target rata-rata nilai order'),
                        
                        Forms\Components\TextInput::make('likes_per_minute')
                            ->label('Like per Menit')
                            ->required()
                            ->numeric()
                            ->default(300)
                            ->helperText('Target jumlah like per menit'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gmv_per_hour')
                    ->label('GMV per Jam')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Conversion Rate')
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('aov')
                    ->label('AOV')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('likes_per_minute')
                    ->label('Like/Menit')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Disable bulk delete for KPI targets
            ]);
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
            'index' => Pages\ListKpiTargets::route('/'),
            'create' => Pages\CreateKpiTarget::route('/create'),
            'edit' => Pages\EditKpiTarget::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        // Only allow one KPI target record
        return KpiTarget::count() === 0;
    }
}

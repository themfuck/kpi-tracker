<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiveSessionResource\Pages;
use App\Filament\Resources\LiveSessionResource\RelationManagers;
use App\Models\LiveSession;
use App\Models\Host;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LiveSessionResource extends Resource
{
    protected static ?string $model = LiveSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    
    protected static ?string $navigationLabel = 'Live Session';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?string $pluralModelLabel = 'Live Sessions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Live Session')
                    ->schema([
                        Forms\Components\Select::make('host_id')
                            ->label('Host')
                            ->relationship('host', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\Select::make('role')
                                    ->options([
                                        'Host' => 'Host',
                                        'Operator' => 'Operator',
                                    ])
                                    ->default('Host')
                                    ->required(),
                            ]),
                        
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Live')
                            ->required()
                            ->default(now())
                            ->native(false),
                        
                        Forms\Components\TextInput::make('hours_live')
                            ->label('Jam Live')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0)
                            ->suffix('jam'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Metrik Performa')
                    ->schema([
                        Forms\Components\TextInput::make('gmv')
                            ->label('GMV (Gross Merchandise Value)')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->placeholder('0'),
                        
                        Forms\Components\TextInput::make('orders')
                            ->label('Jumlah Order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('viewers')
                            ->label('Jumlah Viewer')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('likes')
                            ->label('Jumlah Like')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('errors')
                            ->label('Jumlah Error')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Jumlah error/gangguan selama live'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('host.name')
                    ->label('Host')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hours_live')
                    ->label('Jam Live')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' jam')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('gmv')
                    ->label('GMV')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total GMV'),
                    ]),
                
                Tables\Columns\TextColumn::make('orders')
                    ->label('Orders')
                    ->numeric()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Orders'),
                    ]),
                
                Tables\Columns\TextColumn::make('viewers')
                    ->label('Viewers')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('likes')
                    ->label('Likes')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('errors')
                    ->label('Errors')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('host_id')
                    ->label('Host')
                    ->relationship('host', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename('live-sessions-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                        ]),
                ]),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListLiveSessions::route('/'),
            'create' => Pages\CreateLiveSession::route('/create'),
            'edit' => Pages\EditLiveSession::route('/{record}/edit'),
        ];
    }
}

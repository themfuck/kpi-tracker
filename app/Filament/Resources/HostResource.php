<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HostResource\Pages;
use App\Filament\Resources\HostResource\RelationManagers;
use App\Models\Host;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HostResource extends Resource
{
    protected static ?string $model = Host::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Host';
    
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Host')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Host')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'Host' => 'Host',
                                'Operator' => 'Operator',
                            ])
                            ->required()
                            ->default('Host'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true),
                        
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('host-photos')
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Host' => 'success',
                        'Operator' => 'info',
                    })
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('liveSessions_count')
                    ->label('Total Sesi')
                    ->counts('liveSessions')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'Host' => 'Host',
                        'Operator' => 'Operator',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListHosts::route('/'),
            'create' => Pages\CreateHost::route('/create'),
            'edit' => Pages\EditHost::route('/{record}/edit'),
        ];
    }
}

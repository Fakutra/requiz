<?php

namespace App\Filament\Resources;

use App\Models\Position;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Filament\Resources\PositionResource\Pages;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;
    protected static ?string $navigationLabel = 'Kelola Lowongan';
    protected static ?string $navigationGroup = 'Manajemen';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('position')->required(),
            Forms\Components\TextInput::make('quota')->numeric()->required(),
            Forms\Components\DatePicker::make('tanggal')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('position'),
            Tables\Columns\TextColumn::make('quota'),
            Tables\Columns\TextColumn::make('tanggal')->date(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}

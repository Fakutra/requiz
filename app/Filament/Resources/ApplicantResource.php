<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Models\Applicant;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationLabel = 'Data Pelamar';
    protected static ?string $pluralModelLabel = 'Data Pelamar';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nik'),
            TextColumn::make('pendidikan'),
            TextColumn::make('domisili'),
            TextColumn::make('cv')
                ->label('CV')
                ->formatStateUsing(fn ($state) => $state ? 'Download' : '-')
                ->url(fn ($record) => $record->cv ? asset('storage/' . $record->cv) : null, true)
                ->openUrlInNewTab()
                ->color('primary'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicants::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}

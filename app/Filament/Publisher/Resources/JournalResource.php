<?php

namespace App\Filament\Publisher\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Journal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $modelLabel = 'Jurnal';
    protected static ?string $modelPluralLabel = 'Jurnal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Jurnal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Jurnal')
                                    ->placeholder('Masukkan nama jurnal')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('price')
                                    ->label('Harga (IDR)')
                                    ->placeholder('0')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                                    ->stripCharacters('.')
                                    ->numeric()
                                    ->required(),
                            ]),
                    ]),

                Section::make('Scope & Thumbnail')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValue::make('scope')
                                    ->label('Scope')
                                    ->keyLabel('Slug')
                                    ->valueLabel('Nama')
                                    ->required(),

                                FileUpload::make('thumbnail')
                                    ->label('Thumbnail Cover')
                                    ->image()
                                    ->helperText('Format JPG/PNG, max 2MB')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Opsi Publikasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('publish_months')
                                    ->label('Bulan Terbit')
                                    ->options([
                                        '1'  => 'Januari',
                                        '2'  => 'Februari',
                                        '3'  => 'Maret',
                                        '4'  => 'April',
                                        '5'  => 'Mei',
                                        '6'  => 'Juni',
                                        '7'  => 'Juli',
                                        '8'  => 'Agustus',
                                        '9'  => 'September',
                                        '10' => 'Oktober',
                                        '11' => 'November',
                                        '12' => 'Desember',
                                    ])
                                    ->multiple()
                                    ->required(),

                                Toggle::make('is_active')
                                    ->visible(fn($record) => $record->already_edit)
                                    ->label('Aktif?')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('publisher_id', static::getPublisher()->id))
            ->poll('10s')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('scope')
                    ->label('Scope')
                    ->limit(50),

                TextColumn::make('path')
                    ->label('Path')
                    ->sortable(),

                ImageColumn::make('thumbnail')
                    ->label('Thumbnail'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif')
                    ->sortable(),

                TextColumn::make('publish_months')
                    ->label('Bulan Terbit')
                    ->formatStateUsing(fn($record) => collect($record->publish_months)
                        ->map(fn($m) => [
                            '1' => 'Jan',
                            '2' => 'Feb',
                            '3' => 'Mar',
                            '4' => 'Apr',
                            '5' => 'Mei',
                            '6' => 'Jun',
                            '7' => 'Jul',
                            '8' => 'Agt',
                            '9' => 'Sep',
                            '10' => 'Okt',
                            '11' => 'Nov',
                            '12' => 'Des'
                        ][$m])
                        ->join(', ')),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Status Aktif')
                    ->toggle(),

                SelectFilter::make('publish_months')
                    ->multiple()
                    ->label('Filter Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function($data, $record) {
                        if (!$record->already_edit && !$record->is_active) {
                            $data['already_edit'] = 1;
                            $data['is_active'] = 1;
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function getPublisher()
    {
        return once(fn() => auth()->user()->publisher);
    }

    public static function getPages(): array
    {
        return [
            'index' => JournalResource\Pages\ManageJournals::route('/'),
        ];
    }
}

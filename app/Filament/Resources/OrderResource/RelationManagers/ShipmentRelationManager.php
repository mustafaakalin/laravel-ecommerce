<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'shipment';
    protected static ?string $title = 'Kargo Bilgileri';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('carrier')
                    ->required()
                    ->options([
                        'ups' => 'UPS',
                        'fedex' => 'FedEx',
                        'dhl' => 'DHL',
                        'aras' => 'Aras Kargo',
                        'yurtici' => 'Yurtiçi Kargo',
                        'mng' => 'MNG Kargo',
                        'ptt' => 'PTT Kargo',
                    ])
                    ->searchable()
                    ->label('Kargo Firması'),

                Forms\Components\TextInput::make('tracking_number')
                    ->required()
                    ->label('Takip Numarası')
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('shipped_at')
                    ->label('Kargoya Verilme Tarihi')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('carrier')
                    ->label('Kargo Firması'),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Takip Numarası')
                    ->copyable(),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Kargoya Verilme Tarihi')
                    ->dateTime(),
            ]);
    }
}
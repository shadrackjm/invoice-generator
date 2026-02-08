<?php

namespace App\Filament\Resources\Invoices\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->prefix('$')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->alignRight(),
                TextColumn::make('unit_price')
                    ->money('USD')
                    ->alignRight(),
                TextColumn::make('total')
                    ->money('USD')
                    ->alignRight()
                    ->getStateUsing(fn($record) => $record->quantity * $record->unit_price),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotals();
                    }),
                AssociateAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotals();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotals();
                    }),
                DissociateAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotals();
                    }),
                DeleteAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->calculateTotals();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->after(function () {
                            $this->getOwnerRecord()->calculateTotals();
                        }),
                    DeleteBulkAction::make()
                        ->after(function () {
                            $this->getOwnerRecord()->calculateTotals();
                        }),
                ]),
            ]);
    }
}

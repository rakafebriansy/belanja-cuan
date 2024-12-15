<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                ->label('Order ID')
                ->searchable(),
                TextColumn::make('grand_total')
                ->money('IDR'),
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match($state) {
                    'ordered' => 'info',
                    'processing' => 'warning',
                    'shipped' => 'warning',
                    'delivered' => 'success',
                    'canceled' => 'danger',
                })
                ->icon(fn (string $state): string => match($state) {
                    'ordered' => 'heroicon-m-sparkles',
                    'processing' => 'heroicon-m-arrow-path',
                    'shipped' => 'heroicon-m-truck',
                    'delivered' => 'heroicon-m-check',
                    'canceled' => 'heroicon-m-x-circle',
                })
                ->sortable(),
                TextColumn::make('payment_status')
                ->searchable()
                ->sortable()
                ->badge(),
                TextColumn::make('payment_method')
                ->searchable()
                ->sortable(),
                TextColumn::make('created_at')
                ->label('Order Date')
                ->dateTime()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('View')
                ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                ->color('info')
                ->icon('heroicon-o-eye'), // membuat custom action button berisi url redirect ke url page yang pernah dibuat sebelumnya pada resource ini (tidak bisa menggunakan default button karena bukan di resource sendiri)
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

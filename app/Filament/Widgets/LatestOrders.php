<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full'; // mengatur column span
    protected static ?int $sort = 2; // mengatur urutan dari widget
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery()) // mendapatkan query eloquent dari model (bukan query builder)
            ->defaultPaginationPageOption(5) // jumlah record dalam satu page
            ->defaultSort('created_at','desc') // setelan order data default
            ->columns([
                TextColumn::make('id')
                ->label('Order ID')
                ->searchable(),
                TextColumn::make('user.name')
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
            ->actions([
                Action::make('view')
                ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                ->icon('heroicon-o-eye')
            ]);
    }
}

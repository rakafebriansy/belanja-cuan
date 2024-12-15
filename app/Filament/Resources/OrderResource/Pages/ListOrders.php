<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array // function untuk menampilkan widget pada header list
    {
        return [
            OrderStats::class
        ]; // menampilkan order stats widgets
    }

    public function getTabs(): array // function untuk menampilkan widget pada header list
    {
        return [
            null => Tab::make('All'),
            'new' => Tab::make()->query(fn ($query) => $query->where('status','ordered')),
            'processing' => Tab::make()->query(fn ($query) => $query->where('status','processing')),
            'shipped' => Tab::make()->query(fn ($query) => $query->where('status','shipped')),
            'delivered' => Tab::make()->query(fn ($query) => $query->where('status','delivered')),
            'canceled' => Tab::make()->query(fn ($query) => $query->where('status','canceled')),
        ];
    }
}

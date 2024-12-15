<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                        ->relationship('user','name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        Select::make('payment_method')
                        ->options([
                            'stripe' => 'Stripe',
                            'cod' => 'Cash on Delivery'
                        ])
                        ->required(),
                        Select::make('payment_status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('ordered')
                        ->required(),
                        ToggleButtons::make('status')
                        ->options([
                            'ordered' => 'Ordered',
                            'processing' => 'Processing',
                            'shipping' => 'Shipping',
                            'delivered' => 'Delivered',
                            'canceled' => 'Canceled',
                        ])
                        ->inline()
                        ->default('ordered')
                        ->required()
                        ->colors([
                            'ordered' => 'info',
                            'processing' => 'warning',
                            'shipping' => 'warning',
                            'delivered' => 'success',
                            'canceled' => 'danger',
                        ])
                        ->icons([
                            'ordered' => 'heroicon-m-sparkles',
                            'processing' => 'heroicon-m-arrow-path',
                            'shipping' => 'heroicon-m-truck',
                            'delivered' => 'heroicon-m-check-badge',
                            'canceled' => 'heroicon-m-x-circle',
                        ]),
                        Select::make('currency')
                        ->options([
                            'idr' => 'IDR',
                            'usd' => 'USD',
                            'eur' => 'EUR',
                            'inr' => 'INR'
                        ])
                        ->default('IDR')
                        ->required(),
                        Select::make('shipping_method')
                        ->options([
                            'fedex' => 'FedEx',
                            'ups' => 'UPS',
                            'jnt' => 'JNT',
                            'jne' => 'JNE'
                        ])
                        ->required(),
                        Textarea::make('notes')
                        ->columnSpanFull()
                    ])->columns(2),
                    Section::make('Order Detail')->schema([
                        Repeater::make('orderDetails')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                            ->relationship('product','name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->reactive() //change in this field will trigger change in another field (client-side)
                            ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                            ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set) => $set('total_amount', Product::find($state)?->price ?? 0))
                            ->columnSpan(4),
                            TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) => $set('total_amount', $state*$get('unit_amount')))
                            ->columnSpan(2),
                            TextInput::make('unit_amount')
                            ->numeric()
                            ->required()
                            ->dehydrated()
                            ->readOnly()
                            ->columnSpan(3),
                            TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->dehydrated()
                            ->columnSpan(3),
                        ])
                        ->columns(12),
                        Placeholder::make('grand_total_placeholder')
                        ->label('Grand Total')
                        ->content(function(\Filament\Forms\Get $get, \Filament\Forms\Set $set) {
                            $total = 0;
                            if(!$repeaters = $get('orderDetails')) {
                                return $total;
                            } 
                            foreach ($repeaters as $key => $repeater) {
                                $total += $get("orderDetails.{$key}.total_amount");
                            }
                            $set('grand_total',$total);
                            return Number::currency($total,'IDR');
                        }),
                        Hidden::make('grand_total')
                        ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->label('Customer')
                ->sortable()
                ->searchable(),
                TextColumn::make('grand_total')
                ->numeric()
                ->money('IDR')
                ->sortable(),
                TextColumn::make('payment_method')
                ->sortable()
                ->searchable(),
                TextColumn::make('payment_status')
                ->sortable()
                ->searchable(),
                TextColumn::make('currency')
                ->sortable()
                ->searchable(),
                TextColumn::make('shipping_method')
                ->sortable()
                ->searchable(),
                SelectColumn::make('status')
                ->options([
                    'ordered' => 'Ordered',
                    'processing' => 'Processing',
                    'shipping' => 'Shipping',
                    'delivered' => 'Delivered',
                    'canceled' => 'Canceled',
                ])
                ->searchable()
                ->sortable(),
                TextColumn::make('created_at')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime(),
                TextColumn::make('updated_at')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count(); // menampilkan navigation badge berupa jumlah record dari resource pada sidebar
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'danger' : 'success'; // mengubah warna dari navigation badge
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Tables\Filters\TextFilter;
use Filament\Resources\Resource;
use Tables\Filters\DateTimeFilter;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship(name: 'customer', titleAttribute: 'name')
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->preload(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'B' => 'Billed',
                        'P' => 'Paid',
                        'V' => 'Voided',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('billed_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_date'),
                // Hidden::make('customer_id')->value(request()->input('customer_id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billed_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {

                        'P' => 'success',
                        'B' => 'warning',
                        'V' => 'danger',
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'P' => 'Paid',
                            'B' => 'Billed',
                            'V' => 'Voided',
                        };
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //sort according to status, customer and billed date
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'P' => 'Paid',
                        'B' => 'Billed',
                        'V' => 'Voided',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

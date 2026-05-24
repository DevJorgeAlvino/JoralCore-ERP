<?php

namespace App\Filament\Company\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Información Personal')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label(__('users.fields.name'))
                                    ->icon('heroicon-m-user')
                                    ->weight('bold'),

                                TextEntry::make('email')
                                    ->label(__('users.fields.email'))
                                    ->icon('heroicon-m-envelope')
                                    ->copyable(),
                            ]),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Estado de Verificación')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            TextEntry::make('email_verified_at')
                                ->label('Verificado el')
                                ->icon('heroicon-m-check-badge')
                                ->dateTime()
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'warning')
                                ->formatStateUsing(fn ($state) => $state ? $state : 'No verificado'),
                        ]),
                ])->columnSpan(['lg' => 1]),

                Section::make('Metadatos')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('id')
                                ->label('ID (ULID)')
                                ->copyable()
                                ->badge()
                                ->color('gray'),

                            TextEntry::make('created_at')
                                ->label(__('users.fields.created_at'))
                                ->dateTime()
                                ->placeholder('-'),

                            TextEntry::make('updated_at')
                                ->label(__('users.fields.updated_at'))
                                ->dateTime()
                                ->placeholder('-'),

                            TextEntry::make('deleted_at')
                                ->label(__('users.fields.deleted_at'))
                                ->dateTime()
                                ->visible(fn (User $record): bool => $record->trashed()),
                        ]),
                    ]),
            ])
            ->columns(['lg' => 3]);
    }
}

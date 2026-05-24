<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── Información Comercial ───────────────────
                Section::make('Información Comercial')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre Comercial'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('description')
                            ->label('Descripción')
                            ->placeholder('Sin descripción.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Localización y Configuración Regional ───
                Section::make('Localización y Configuración Regional')
                    ->icon('heroicon-o-globe-americas')
                    ->schema([
                        TextEntry::make('country')
                            ->label('País')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'PE' => '🇵🇪 Perú',
                                'CL' => '🇨🇱 Chile',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'PE' => 'danger',
                                'CL' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('currency')
                            ->label('Moneda Base')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'PEN' => 'PEN — Sol (S/)',
                                'CLP' => 'CLP — Peso ($)',
                                default => $state,
                            }),

                        TextEntry::make('timezone')
                            ->label('Zona Horaria')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ─── Información Legal y Tributaria ──────────
                Section::make('Información Legal y Tributaria')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('legal_name')
                            ->label('Razón Social')
                            ->weight('bold')
                            ->columnSpanFull(),

                        TextEntry::make('identity_document')
                            ->label(fn (Company $record): string => $record->country === 'CL' ? 'RUT' : 'RUC')
                            ->copyable()
                            ->icon('heroicon-o-identification'),

                        TextEntry::make('dv')
                            ->label('Dígito Verificador')
                            ->badge()
                            ->color('warning')
                            ->visible(fn (Company $record): bool => $record->country === 'CL'),

                        TextEntry::make('formatted_document')
                            ->label('Documento Completo')
                            ->badge()
                            ->color('success')
                            ->visible(fn (Company $record): bool => $record->country === 'CL'),

                        TextEntry::make('economic_activity_code')
                            ->label(fn (Company $record): string => $record->country === 'CL'
                                ? 'Código Actividad SII'
                                : 'Código CIIU')
                            ->placeholder('No registrado.'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ─── Dirección Fiscal y Contacto ─────────────
                Section::make('Dirección Fiscal y Contacto')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextEntry::make('tax_address')
                            ->label('Dirección Fiscal')
                            ->icon('heroicon-o-map')
                            ->columnSpanFull(),

                        TextEntry::make('geo_code')
                            ->label(fn (Company $record): string => $record->country === 'CL'
                                ? 'Código de Comuna'
                                : 'Ubigeo')
                            ->placeholder('No registrado.'),

                        TextEntry::make('phone')
                            ->label('Teléfono Corporativo')
                            ->icon('heroicon-o-phone')
                            ->placeholder('No registrado.'),

                        TextEntry::make('email')
                            ->label('Correo Corporativo')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->placeholder('No registrado.'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ─── Metadatos del Registro ──────────────────
                Section::make('Metadatos del Registro')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID (ULID)')
                            ->copyable()
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('deleted_at')
                            ->label('Eliminado el')
                            ->dateTime()
                            ->visible(fn (Company $record): bool => $record->trashed()),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}

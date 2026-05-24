<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    // ─── Información Comercial ───────────────────
                        Section::make(__('companies.sections.commercial'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('name')
                                        ->label(__('companies.fields.name'))
                                        ->icon('heroicon-m-building-office')
                                        ->weight('bold'),

                                    TextEntry::make('slug')
                                        ->label(__('companies.fields.slug'))
                                        ->badge()
                                        ->icon('heroicon-m-link')
                                        ->color('gray'),

                                    \Filament\Infolists\Components\IconEntry::make('is_active')
                                        ->label('Cuenta Activa')
                                        ->boolean()
                                        ->columnSpanFull(),

                                    TextEntry::make('description')
                                        ->label(__('companies.fields.description'))
                                        ->placeholder('Sin descripción.')
                                        ->columnSpanFull(),
                                ]),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                    Group::make()->schema([
                        // ─── Localización y Configuración Regional ───
                        Section::make(__('companies.sections.localization'))
                            ->icon('heroicon-o-globe-americas')
                            ->schema([
                                TextEntry::make('country')
                                    ->label(__('companies.fields.country'))
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
                                    ->label(__('companies.fields.currency'))
                                    ->badge()
                                    ->icon('heroicon-m-currency-dollar')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'PEN' => 'PEN — Sol (S/)',
                                        'CLP' => 'CLP — Peso ($)',
                                        default => $state,
                                    }),

                                TextEntry::make('timezone')
                                    ->label(__('companies.fields.timezone'))
                                    ->icon('heroicon-o-clock'),
                            ]),

                        // ─── Metadatos del Registro ──────────────────
                        Section::make('Metadatos')
                            ->icon('heroicon-o-information-circle')
                            ->collapsed()
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID (ULID)')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('created_at')
                                    ->label(__('companies.fields.created_at'))
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label(__('companies.fields.updated_at'))
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('deleted_at')
                                    ->label(__('companies.fields.deleted_at'))
                                    ->dateTime()
                                    ->visible(fn (Company $record): bool => $record->trashed()),
                            ]),
                    ])->columnSpan(['lg' => 1]),

                // ─── Información Legal y Tributaria ──────────
                Section::make(__('companies.sections.legal'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('legal_name')
                                ->label(__('companies.fields.legal_name'))
                                ->weight('bold')
                                ->icon('heroicon-m-scale')
                                ->columnSpan(2),

                            TextEntry::make('identity_document')
                                ->label(fn (Company $record): string => $record->country === 'CL' ? 'RUT' : 'RUC')
                                ->copyable()
                                ->icon('heroicon-o-identification'),

                            TextEntry::make('dv')
                                ->label(__('companies.fields.dv'))
                                ->badge()
                                ->color('warning')
                                ->visible(fn (Company $record): bool => $record->country === 'CL'),

                            TextEntry::make('economic_activity_code')
                                ->label(fn (Company $record): string => $record->country === 'CL'
                                    ? 'Código Actividad SII'
                                    : 'Código CIIU')
                                ->icon('heroicon-m-briefcase')
                                ->placeholder('No registrado.'),

                            TextEntry::make('tax_regime')
                                ->label('Régimen Tributario')
                                ->badge()
                                ->icon('heroicon-m-document-currency-dollar')
                                ->placeholder('No registrado.'),

                            \Filament\Infolists\Components\IconEntry::make('is_retention_agent')
                                ->label('Agente de Retención')
                                ->boolean(),

                            TextEntry::make('legal_rep_name')
                                ->label('Representante Legal')
                                ->icon('heroicon-m-user-circle')
                                ->columnSpan(2)
                                ->placeholder('No registrado.'),

                            TextEntry::make('legal_rep_document')
                                ->label('Doc. Representante')
                                ->icon('heroicon-m-identification')
                                ->placeholder('No registrado.'),
                        ]),
                    ]),

                // ─── Dirección Fiscal y Contacto ─────────────
                Section::make(__('companies.sections.contact'))
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('tax_address')
                            ->label(__('companies.fields.tax_address'))
                            ->icon('heroicon-o-map')
                            ->columnSpanFull(),

                        Grid::make(4)->schema([
                            TextEntry::make('geo_code')
                                ->label(fn (Company $record): string => $record->country === 'CL'
                                    ? 'Código de Comuna'
                                    : 'Ubigeo')
                                ->placeholder('No registrado.'),

                            TextEntry::make('phone')
                                ->label(__('companies.fields.phone'))
                                ->icon('heroicon-o-phone')
                                ->placeholder('No registrado.'),

                            TextEntry::make('email')
                                ->label(__('companies.fields.email'))
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->placeholder('No registrado.'),

                            TextEntry::make('website')
                                ->label('Sitio Web')
                                ->icon('heroicon-m-globe-alt')
                                ->url(fn (Company $record): ?string => $record->website)
                                ->placeholder('No registrado.'),
                        ]),
                    ]),
            ])
            ->columns(['lg' => 3]);
    }
}

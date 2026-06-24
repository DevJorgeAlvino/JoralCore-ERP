<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([

                // ── Left (main) ─────────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 2])->schema([

                    Section::make('Información de la Marca')
                        ->description('Nombre e identidad de la marca o fabricante.')
                        ->icon('heroicon-o-building-storefront')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre de la Marca')
                                ->placeholder('Ej. Samsung, Coca-Cola, Nike')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                ->columnSpanFull(),

                            TextInput::make('slug')
                                ->label('Slug')
                                ->placeholder('Se genera automáticamente')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Identificador único. Solo letras minúsculas, números y guiones.'),

                            TextInput::make('website')
                                ->label('Sitio Web')
                                ->placeholder('https://www.ejemplo.com')
                                ->url()
                                ->maxLength(255),
                        ])->columns(2),

                    Section::make('Logo de la Marca')
                        ->description('Imagen representativa de la marca.')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            FileUpload::make('logo')
                                ->label('Logo')
                                ->image()
                                ->maxSize(2048)
                                ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                ->visibility('public')
                                ->directory(function () {
                                    $companyId = Filament::getTenant()?->id;

                                    return "companies/company_{$companyId}/brands/logos";
                                })
                                ->imagePreviewHeight('120')
                                ->columnSpanFull(),
                        ]),
                ]),

                // ── Right (settings) ────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 1])->schema([

                    Section::make('Empresa')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Select::make('company_id')
                                ->label('Empresa')
                                ->relationship('company', 'name')
                                ->native(false)
                                ->required()
                                ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin'),
                        ]),

                    Section::make('Estado')->schema([
                        Toggle::make('is_active')
                            ->label('Marca Activa')
                            ->helperText('Las marcas inactivas no aparecen en los formularios de ítems.')
                            ->default(true),
                    ]),
                ]),
            ]),
        ]);
    }
}

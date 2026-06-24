<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([

                // ── Left column (main content) ──────────────────────────────
                Group::make()->columnSpan(['lg' => 2])->schema([

                    Section::make('Información de la Categoría')
                        ->description('Nombre, descripción e identificación visual.')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->placeholder('Ej. Electrónica, Bebidas, Ropa Deportiva')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                ->columnSpanFull(),

                            TextInput::make('slug')
                                ->label('Slug (URL)')
                                ->placeholder('Se genera automáticamente')
                                ->required()
                                ->maxLength(255)
                                ->unique(table: 'categories', column: 'slug', ignoreRecord: true)
                                ->helperText('Identificador único para URLs. Solo letras minúsculas, números y guiones.'),

                            Textarea::make('description')
                                ->label('Descripción')
                                ->placeholder('Describe brevemente los productos o servicios de esta categoría...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),

                    Section::make('Apariencia')
                        ->description('Ícono y color para identificar visualmente la categoría.')
                        ->icon('heroicon-o-paint-brush')
                        ->schema([
                            TextInput::make('icon')
                                ->label('Ícono (Heroicon)')
                                ->placeholder('heroicon-o-tag')
                                ->helperText('Ingresa el nombre de un Heroicon. Ej: heroicon-o-shopping-bag')
                                ->maxLength(100),

                            ColorPicker::make('color')
                                ->label('Color de Identificación')
                                ->helperText('Color para distinguir la categoría en listas y reportes.'),
                        ])->columns(2),
                ]),

                // ── Right column (settings) ─────────────────────────────────
                Group::make()->columnSpan(['lg' => 1])->schema([

                    Section::make('Organización')
                        ->description('Clasifica esta categoría dentro de la jerarquía.')
                        ->icon('heroicon-o-folder-open')
                        ->schema([
                            Select::make('company_id')
                                ->label('Empresa')
                                ->relationship('company', 'name')
                                ->native(false)
                                ->required()
                                ->visible(fn () => Filament::getCurrentPanel()?->getId() === 'admin')
                                ->live(),

                            Select::make('parent_id')
                                ->label('Categoría Padre')
                                ->placeholder('Sin categoría padre (raíz)')
                                ->options(function (Get $get, ?Category $record) {
                                    $companyId = Filament::getCurrentPanel()?->getId() === 'admin'
                                        ? $get('company_id')
                                        : Filament::getTenant()?->id;

                                    if (! $companyId) {
                                        return [];
                                    }

                                    return Category::query()
                                        ->where('company_id', $companyId)
                                        ->when($record?->id, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->native(false)
                                ->helperText('Déjalo vacío para crear una categoría raíz.'),
                        ]),

                    Section::make('Estado')->schema([
                        Toggle::make('is_active')
                            ->label('Categoría Activa')
                            ->helperText('Las categorías inactivas no aparecen en los formularios de ítems.')
                            ->default(true),
                    ]),
                ]),
            ]),
        ]);
    }
}

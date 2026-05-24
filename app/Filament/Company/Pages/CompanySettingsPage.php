<?php

namespace App\Filament\Company\Pages;

use App\Services\CompanySettingService;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Storage;

class CompanySettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configuración';

    protected static ?string $title = 'Configuración de la Empresa';

    protected static string | UnitEnum | null $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.company.pages.company-settings';

    // ─── Estado del formulario ───────────────────
    public ?array $data = [];

    public function mount(): void
    {
        $companyId = Filament::getTenant()->id;
        $settings = CompanySettingService::allFor($companyId);

        $this->form->fill([
            // Branding
            'company_logo'       => $settings['company_logo'] ?? null,
            'company_icon'       => $settings['company_icon'] ?? null,
            'primary_color'      => $settings['primary_color'] ?? '#f59e0b',
            'secondary_color'    => $settings['secondary_color'] ?? '#6366f1',
            'locale'             => $settings['locale'] ?? 'es',
            // Operaciones
            'business_hours'     => $settings['business_hours'] ?? $this->defaultBusinessHours(),
            'secondary_currency' => $settings['secondary_currency'] ?? null,
            'exchange_rate'      => $settings['exchange_rate'] ?? null,
            'stock_alert_min'    => $settings['stock_alert_min'] ?? 10,
            'stock_alert_enabled' => $settings['stock_alert_enabled'] ?? true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                // ─── Branding de la Empresa ──────────────────
                Section::make('Branding de la Empresa')
                    ->description('Logo, ícono y colores del panel para esta empresa.')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        FileUpload::make('company_logo')
                            ->label('Logo de la Empresa')
                            ->image()
                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                            ->directory(function () {
                                $tenantUlid = filament()->getTenant()?->id;
                                return "companies/company_{$tenantUlid}/branding";
                            })
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Recomendado: PNG transparente, 400×100px máximo. Máx 2MB.')
                            ->columnSpanFull(),

                        FileUpload::make('company_icon')
                            ->label('Ícono / Favicon')
                            ->image()
                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                            ->directory(function () {
                                $tenantUlid = filament()->getTenant()?->id;
                                return "companies/company_{$tenantUlid}/branding";
                            })
                            ->visibility('public')
                            ->maxSize(512)
                            ->helperText('Se muestra en la pestaña del navegador. Recomendado: 32×32px.'),

                        ColorPicker::make('primary_color')
                            ->label('Color Primario')
                            ->required()
                            ->helperText('Color principal de botones, links y acentos del panel.'),

                        ColorPicker::make('secondary_color')
                            ->label('Color Secundario')
                            ->required()
                            ->helperText('Color para badges y elementos complementarios.'),

                        Select::make('locale')
                            ->label('Idioma de la Empresa')
                            ->options([
                                'es' => '🇪🇸 Español',
                                'en' => '🇺🇸 English',
                                'pt' => '🇧🇷 Português',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Horarios de Atención ────────────────────
                Section::make('Horarios de Atención')
                    ->description('Configura los horarios de operación por día de la semana.')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Repeater::make('business_hours')
                            ->label('')
                            ->schema([
                                Select::make('day')
                                    ->label('Día')
                                    ->options([
                                        'lunes'     => 'Lunes',
                                        'martes'    => 'Martes',
                                        'miércoles' => 'Miércoles',
                                        'jueves'    => 'Jueves',
                                        'viernes'   => 'Viernes',
                                        'sábado'    => 'Sábado',
                                        'domingo'   => 'Domingo',
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('open')
                                    ->label('Apertura')
                                    ->type('time')
                                    ->required(),

                                TextInput::make('close')
                                    ->label('Cierre')
                                    ->type('time')
                                    ->required(),

                                Toggle::make('active')
                                    ->label('Activo')
                                    ->default(true),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // ─── Moneda Secundaria ───────────────────────
                Section::make('Moneda de Operación Secundaria')
                    ->description('Configura una moneda adicional y su tasa de cambio referencial.')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsed()
                    ->schema([
                        Select::make('secondary_currency')
                            ->label('Moneda Secundaria')
                            ->options([
                                'USD' => 'USD — Dólar americano ($)',
                                'EUR' => 'EUR — Euro (€)',
                                'PEN' => 'PEN — Sol peruano (S/)',
                                'CLP' => 'CLP — Peso chileno ($)',
                            ])
                            ->native(false)
                            ->placeholder('Seleccionar moneda...')
                            ->helperText('Moneda adicional para cotizaciones y reportes.'),

                        TextInput::make('exchange_rate')
                            ->label('Tasa de Cambio')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.0001)
                            ->helperText('Tasa de conversión referencial respecto a la moneda base.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Alertas de Stock ────────────────────────
                Section::make('Alertas de Stock')
                    ->description('Parámetros para notificaciones de inventario bajo.')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->collapsed()
                    ->schema([
                        Toggle::make('stock_alert_enabled')
                            ->label('Alertas habilitadas')
                            ->helperText('Activa o desactiva las notificaciones de stock mínimo.'),

                        TextInput::make('stock_alert_min')
                            ->label('Stock Mínimo')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('Cantidad mínima de unidades antes de generar alerta.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $companyId = Filament::getTenant()->id;
        $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';

         // Borrar logo anterior si se sube uno nuevo
        $oldLogo = CompanySettingService::get($companyId, 'company_logo');
        if ($oldLogo && $oldLogo !== $data['company_logo']) {
            if (Storage::disk($disk)->exists($oldLogo)) {
                Storage::disk($disk)->delete($oldLogo);
            }
        }

        // Borrar favicon anterior si se sube uno nuevo
        $oldFavicon = CompanySettingService::get($companyId, 'company_icon');
        if ($oldFavicon && $oldFavicon !== $data['company_icon']) {
            if (Storage::disk($disk)->exists($oldFavicon)) {
                Storage::disk($disk)->delete($oldFavicon);
            }
        }

        foreach ($data as $key => $value) {
            CompanySettingService::set($companyId, $key, $value);
        }

        Notification::make()
            ->title('Configuración guardada')
            ->body('Los cambios de branding se aplicarán en la próxima carga del panel.')
            ->success()
            ->send();
    }

    /**
     * Horarios por defecto (lunes a viernes, 08:00 - 18:00).
     */
    private function defaultBusinessHours(): array
    {
        $days = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'];

        return array_map(fn (string $day) => [
            'day'    => $day,
            'open'   => '08:00',
            'close'  => '18:00',
            'active' => true,
        ], $days);
    }
}

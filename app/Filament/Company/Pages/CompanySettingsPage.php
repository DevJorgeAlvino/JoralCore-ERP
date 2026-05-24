<?php

namespace App\Filament\Company\Pages;

use App\Services\CompanySettingService;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Storage;

class CompanySettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function getNavigationLabel(): string
    {
        return __('settings.company_nav');
    }

    public function getTitle(): string
    {
        return __('settings.company_title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('settings.system_group');
    }

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.company.pages.company-settings';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Guardar Configuración')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

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

                Grid::make(['default' => 1, 'lg' => 2])->schema([
                    
                    // ─── Columna Izquierda ───
                    Group::make()->schema([
                        Section::make(__('settings.sections.company_branding'))
                            ->description(__('settings.sections.company_branding_desc'))
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                FileUpload::make('company_logo')
                                    ->label(__('settings.fields.app_logo'))
                                    ->image()
                                    ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                    ->directory(function () {
                                        $tenantUlid = filament()->getTenant()?->id;
                                        return "companies/company_{$tenantUlid}/branding";
                                    })
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->helperText(__('settings.helpers.logo_admin')),

                                FileUpload::make('company_icon')
                                    ->label(__('settings.fields.app_favicon'))
                                    ->image()
                                    ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                    ->directory(function () {
                                        $tenantUlid = filament()->getTenant()?->id;
                                        return "companies/company_{$tenantUlid}/branding";
                                    })
                                    ->visibility('public')
                                    ->maxSize(512)
                                    ->helperText(__('settings.helpers.favicon')),

                                ColorPicker::make('primary_color')
                                    ->label(__('settings.fields.color_primary'))
                                    ->required()
                                    ->helperText(__('settings.helpers.color_primary')),

                                ColorPicker::make('secondary_color')
                                    ->label(__('settings.fields.color_secondary'))
                                    ->required()
                                    ->helperText(__('settings.helpers.color_secondary')),
                            ]),
                    ])->columnSpan(1),

                    // ─── Columna Derecha ───
                    Group::make()->schema([
                        Section::make(__('settings.sections.currency'))
                            ->description(__('settings.sections.currency_desc'))
                            ->icon('heroicon-o-currency-dollar')
                            ->collapsed()
                            ->schema([
                                Select::make('secondary_currency')
                                    ->label(__('settings.fields.secondary_currency'))
                                    ->options([
                                        'USD' => 'USD — Dólar americano ($)',
                                        'EUR' => 'EUR — Euro (€)',
                                        'PEN' => 'PEN — Sol peruano (S/)',
                                        'CLP' => 'CLP — Peso chileno ($)',
                                    ])
                                    ->native(false)
                                    ->placeholder('Seleccionar moneda...')
                                    ->helperText(__('settings.helpers.currency')),

                                TextInput::make('exchange_rate')
                                    ->label(__('settings.fields.exchange_rate'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.0001)
                                    ->helperText(__('settings.helpers.exchange_rate')),
                            ])->columns(2),

                        Section::make(__('settings.sections.stock'))
                            ->description(__('settings.sections.stock_desc'))
                            ->icon('heroicon-o-exclamation-triangle')
                            ->collapsed()
                            ->schema([
                                Toggle::make('stock_alert_enabled')
                                    ->label(__('settings.fields.stock_alert_enabled'))
                                    ->helperText(__('settings.helpers.stock_toggle')),

                                TextInput::make('stock_alert_min')
                                    ->label(__('settings.fields.stock_alert_min'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->helperText(__('settings.helpers.stock_min')),
                            ])->columns(2),
                    ])->columnSpan(1),
                ]),

                // ─── Ancho Completo (Abajo) ───
                Section::make(__('settings.sections.business_hours'))
                    ->description(__('settings.sections.business_hours_desc'))
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Repeater::make('business_hours')
                            ->label('')
                            ->schema([
                                Select::make('day')
                                    ->label(__('settings.fields.day'))
                                    ->options([
                                        'lunes'     => __('settings.days.lunes'),
                                        'martes'    => __('settings.days.martes'),
                                        'miércoles' => __('settings.days.miércoles'),
                                        'jueves'    => __('settings.days.jueves'),
                                        'viernes'   => __('settings.days.viernes'),
                                        'sábado'    => __('settings.days.sábado'),
                                        'domingo'   => __('settings.days.domingo'),
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('open')
                                    ->label(__('settings.fields.open'))
                                    ->type('time')
                                    ->required(),

                                TextInput::make('close')
                                    ->label(__('settings.fields.close'))
                                    ->type('time')
                                    ->required(),

                                Toggle::make('active')
                                    ->label(__('settings.fields.active'))
                                    ->default(true),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
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
            ->title(__('settings.messages.saved_title'))
            ->body(__('settings.messages.saved_company'))
            ->success()
            ->send();
    }

    /**
     * Horarios por defecto.
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

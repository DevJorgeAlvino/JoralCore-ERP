<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Providers\Filament\AdminPanelProvider;
use App\Services\SystemSettingService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Storage;

class GlobalSettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configuración Global';

    protected static ?string $title = 'Configuración Global del Sistema';

    protected static string | UnitEnum | null $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.global-settings';

    // ─── Estado del formulario ───────────────────
    public ?array $data = [];

    public function mount(): void
    {
        
        $settings = SystemSettingService::all();

        $this->form->fill([
            'app_name'        => $settings['app_name'] ?? 'JoralERP',
            'app_logo'        => $settings['app_logo'] ?? null,
            'app_favicon'     => $settings['app_favicon'] ?? null,
            'color_primary'   => $settings['color_primary'] ?? '#f59e0b',
            'color_secondary' => $settings['color_secondary'] ?? '#6366f1',
            'default_locale'  => $settings['default_locale'] ?? 'es',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                // ─── Branding del Login y Panel Admin ────────
                Section::make('Branding del ERP (Login y Panel Admin)')
                    ->description('Logo y nombre que se muestran en la pantalla de login del ERP y en el panel del administrador global. Esto NO afecta a los paneles de las empresas.')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Nombre de la Aplicación')
                            ->required()
                            ->maxLength(100)
                            ->helperText('Se muestra en la pestaña del navegador y en el login del panel Admin.'),

                        FileUpload::make('app_logo')
                            ->label('Logo del Login')
                            ->image()
                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                            ->directory('branding')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Recomendado: PNG transparente, 400×100px máximo. Máx 2MB.'),

                        FileUpload::make('app_favicon')
                            ->label('Favicon del Panel Admin')
                            ->image()
                            ->directory('branding')
                            ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                            ->visibility('public')
                            ->maxSize(512)
                            ->helperText('Recomendado: ICO o PNG de 32×32px. Máx 512KB.'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // ─── Paleta de Colores del Admin ─────────────
                Section::make('Colores del Panel Admin')
                    ->description('Colores del panel de administración global. Cada empresa configura sus propios colores.')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        ColorPicker::make('color_primary')
                            ->label('Color Primario')
                            ->required()
                            ->helperText('Color principal de botones y acentos del panel Admin.'),

                        ColorPicker::make('color_secondary')
                            ->label('Color Secundario')
                            ->required()
                            ->helperText('Color de elementos secundarios.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ─── Idioma Global ───────────────────────────
                Section::make('Idioma Global del Sistema')
                    ->description('Idioma por defecto. Cada empresa puede sobrescribir este valor en su propia configuración.')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Select::make('default_locale')
                            ->label('Idioma por Defecto')
                            ->options([
                                'es' => '🇪🇸 Español',
                                'en' => '🇺🇸 English',
                                'pt' => '🇧🇷 Português',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';

        // Borrar logo anterior si se sube uno nuevo
        $oldLogo = SystemSettingService::get('app_logo');
        if ($oldLogo && $oldLogo !== $data['app_logo']) {
            if (Storage::disk($disk)->exists($oldLogo)) {
                Storage::disk($disk)->delete($oldLogo);
            }
        }

        // Borrar favicon anterior si se sube uno nuevo
        $oldFavicon = SystemSettingService::get('app_favicon');
        if ($oldFavicon && $oldFavicon !== $data['app_favicon']) {
            if (Storage::disk($disk)->exists($oldFavicon)) {
                Storage::disk($disk)->delete($oldFavicon);
            }
        }

        // Guardar todas las configuraciones
        foreach ($data as $key => $value) {
            SystemSettingService::set($key, $value);
        }

        Notification::make()
            ->title('Configuración guardada')
            ->body('Los cambios globales se aplicarán en la próxima carga del panel Admin.')
            ->success()
            ->send();
    }
}

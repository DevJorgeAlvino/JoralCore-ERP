<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use App\Providers\Filament\AdminPanelProvider;
use App\Services\SystemSettingService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\Storage;

class GlobalSettingsPage extends Page
{
    use \BezhanSalleh\FilamentShield\Traits\HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function getNavigationLabel(): string
    {
        return __('settings.global_nav');
    }

    public function getTitle(): string
    {
        return __('settings.global_title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('settings.system_group');
    }

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.global-settings';

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
        $settings = SystemSettingService::all();

        $this->form->fill([
            'app_name'        => $settings['app_name'] ?? 'JoralERP',
            'app_logo'        => $settings['app_logo'] ?? null,
            'app_favicon'     => $settings['app_favicon'] ?? null,
            'color_primary'   => $settings['color_primary'] ?? '#f59e0b',
            'color_secondary' => $settings['color_secondary'] ?? '#6366f1',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])->schema([
                    
                    // Columna Izquierda
                    Group::make()->schema([
                        Section::make(__('settings.sections.branding'))
                            ->description(__('settings.sections.branding_desc'))
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                TextInput::make('app_name')
                                    ->label(__('settings.fields.app_name'))
                                    ->required()
                                    ->maxLength(100)
                                    ->helperText(__('settings.helpers.app_name')),

                                FileUpload::make('app_logo')
                                    ->label(__('settings.fields.app_logo'))
                                    ->image()
                                    ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                    ->directory('branding')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->helperText(__('settings.helpers.logo_admin')),

                                FileUpload::make('app_favicon')
                                    ->label(__('settings.fields.app_favicon'))
                                    ->image()
                                    ->directory('branding')
                                    ->disk(env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public')
                                    ->visibility('public')
                                    ->maxSize(512)
                                    ->helperText(__('settings.helpers.favicon')),
                            ])
                    ])->columnSpan(1),

                    // Columna Derecha
                    Group::make()->schema([
                        Section::make(__('settings.sections.colors'))
                            ->description(__('settings.sections.colors_desc'))
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                ColorPicker::make('color_primary')
                                    ->label(__('settings.fields.color_primary'))
                                    ->required()
                                    ->helperText(__('settings.helpers.color_primary')),

                                ColorPicker::make('color_secondary')
                                    ->label(__('settings.fields.color_secondary'))
                                    ->required()
                                    ->helperText(__('settings.helpers.color_secondary')),
                            ])->columns(2),
                    ])->columnSpan(1),

                ]),
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
            ->title(__('settings.messages.saved_title'))
            ->body(__('settings.messages.saved_global'))
            ->success()
            ->send();

        $this->redirect(request()->header('Referer'));
    }
}

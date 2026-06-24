<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    /**
     * Prefijo de caché para evitar colisiones.
     */
    private const CACHE_PREFIX = 'system_setting:';

    /**
     * Tiempo de caché en segundos (1 hora).
     */
    private const CACHE_TTL = 3600;

    /**
     * Obtiene el valor de una configuración global.
     * Usa first() + acceso a propiedad para que el model cast (json) se aplique.
     */
    public static function get(string $key, mixed $default = null): mixed
    {

        return Cache::remember(
            self::CACHE_PREFIX.$key,
            self::CACHE_TTL,
            fn () => SystemSetting::where('key', $key)->first()?->value ?? $default,
        ) ?? $default;
    }

    /**
     * Establece el valor de una configuración global.
     */
    public static function set(string $key, mixed $value): void
    {
        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget(self::CACHE_PREFIX.$key);
        Cache::forget(self::CACHE_PREFIX.'all');
    }

    /**
     * Obtiene todas las configuraciones globales como array asociativo.
     * Usa get() + mapWithKeys para que los model casts se apliquen correctamente.
     */
    public static function all(): array
    {

        return Cache::remember(
            self::CACHE_PREFIX.'all',
            self::CACHE_TTL,
            fn () => SystemSetting::all()
                ->mapWithKeys(fn (SystemSetting $s) => [$s->key => $s->value])
                ->toArray(),
        );
    }

    /**
     * Elimina una configuración global.
     */
    public static function forget(string $key): void
    {
        SystemSetting::where('key', $key)->delete();

        Cache::forget(self::CACHE_PREFIX.$key);
        Cache::forget(self::CACHE_PREFIX.'all');
    }
}

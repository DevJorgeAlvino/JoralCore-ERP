<?php

namespace App\Services;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Cache;

class CompanySettingService
{
    /**
     * Prefijo de caché para evitar colisiones.
     */
    private const CACHE_PREFIX = 'company_setting:';

    /**
     * Tiempo de caché en segundos (1 hora).
     */
    private const CACHE_TTL = 3600;

    /**
     * Obtiene el valor de una configuración para una empresa específica.
     * Usa first() + acceso a propiedad para que el model cast (json) se aplique.
     */
    public static function get(string $companyId, string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX . $companyId . ':' . $key,
            self::CACHE_TTL,
            fn () => CompanySetting::where('company_id', $companyId)
                ->where('key', $key)
                ->first()?->value ?? $default,
        );
    }

    /**
     * Establece el valor de una configuración para una empresa.
     */
    public static function set(string $companyId, string $key, mixed $value): void
    {
        CompanySetting::updateOrCreate(
            ['company_id' => $companyId, 'key' => $key],
            ['value' => $value],
        );

        Cache::forget(self::CACHE_PREFIX . $companyId . ':' . $key);
        Cache::forget(self::CACHE_PREFIX . $companyId . ':all');
    }

    /**
     * Obtiene todas las configuraciones de una empresa como array asociativo.
     * Usa get() + mapWithKeys para que los model casts se apliquen correctamente.
     */
    public static function allFor(string $companyId): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . $companyId . ':all',
            self::CACHE_TTL,
            fn () => CompanySetting::where('company_id', $companyId)
                ->get()
                ->mapWithKeys(fn (CompanySetting $s) => [$s->key => $s->value])
                ->toArray(),
        );
    }

    /**
     * Elimina una configuración de una empresa.
     */
    public static function forget(string $companyId, string $key): void
    {
        CompanySetting::where('company_id', $companyId)
            ->where('key', $key)
            ->delete();

        Cache::forget(self::CACHE_PREFIX . $companyId . ':' . $key);
        Cache::forget(self::CACHE_PREFIX . $companyId . ':all');
    }
}

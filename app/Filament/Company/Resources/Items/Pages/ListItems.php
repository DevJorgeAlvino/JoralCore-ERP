<?php

namespace App\Filament\Company\Resources\Items\Pages;

use App\Filament\Company\Resources\Items\ItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\ItemImporter::class)
                ->label('Importar Ítems')
                ->modalDescription('El sistema procesa y mapea nativamente archivos CSV para asegurar un rendimiento óptimo. Si usas Excel, guarda tu archivo como .csv antes de subirlo.')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                // Inyectamos el company_id en las opciones AHORA (contexto HTTP con Tenant activo)
                // Para que el job en background pueda leerlo desde $this->options['company_id']
                ->options(['company_id' => filament()->getTenant()?->id]),

            CreateAction::make()
                ->modalWidth('7xl')
                ->extraModalWindowAttributes(['novalidate' => true])
                ->after(function ($record) {
                    
                    $disk = env('CLOUDFLARE_R2_ENDPOINT') ? 'r2_public' : 'public';
                    $storage = Storage::disk($disk);

                    $companyId = $record->company_id; 
                    $itemId = $record->id; 

                    if ($record->images && is_array($record->images)) {
                        $newImages = [];

                        foreach ($record->images as $imagePath) {
                            // 1. Validamos si la imagen está atrapada en la carpeta temporal
                            if (Str::contains($imagePath, '/tmp/images/')) {
                                $filename = basename($imagePath);
                                
                                // 2. Armamos la ruta definitiva con tu prefijo "company_"
                                $newPath = "companies/company_{$companyId}/items/{$itemId}/images/{$filename}";
                                
                                // 3. Ejecutamos la mudanza real en Cloudflare R2 o Local
                                if ($storage->exists($imagePath)) {
                                    $storage->move($imagePath, $newPath);
                                }
                                
                                $newImages[] = $newPath;
                            } else {
                                $newImages[] = $imagePath;
                            }
                        }

                        // 4. Actualizamos de forma silenciosa la columna JSON en DBngin
                        $record->updateQuietly([
                            'images' => $newImages
                        ]);
                    }
                }),
        ];
    }
}

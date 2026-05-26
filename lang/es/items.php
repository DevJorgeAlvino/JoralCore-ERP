<?php

return [
    'title' => 'Maestro de Ítems',
    'single' => 'Ítem',

    'table' => [
        'item' => 'Artículo',
        'stock' => 'Stock',
        'type' => 'Tipo',
    ],

    'actions' => [
        'import' => 'Importar Ítems',
        'import_desc' => 'El sistema procesa y mapea nativamente archivos CSV para asegurar un rendimiento óptimo. Si usas Excel, guarda tu archivo como .csv antes de subirlo.<br><br><strong><span style="color: #eab308;">⚠️ Límite máximo: 10,000 registros por archivo.</span></strong>',
    ],

    'infolist' => [
        'sections' => [
            'main_info' => 'Información Principal',
            'main_info_desc' => 'Datos básicos de identificación y descripción del artículo.',
            'prices_taxes' => 'Precios e Impuestos',
            'prices_taxes_desc' => 'Configuración de costos, precios de venta y detalles fiscales.',
            'gallery' => 'Galería de Imágenes',
            'gallery_desc' => 'Fotos y material visual del artículo.',
            'organization' => 'Organización',
            'organization_desc' => 'Clasificación y disponibilidad en el sistema.',
            'inventory' => 'Inventario',
            'inventory_desc' => 'Gestión de stock actual y niveles mínimos para alertas.',
            'metadata' => 'Metadatos',
            'metadata_desc' => 'Registro de auditoría.',
        ],
        'fields' => [
            'name' => 'Nombre',
            'sku' => 'SKU',
            'barcode' => 'Código de Barras',
            'slug' => 'Slug',
            'description' => 'Descripción',
            'purchase_cost' => 'Costo de Compra',
            'sale_price' => 'Precio de Venta',
            'tax_type' => 'Tipo de Impuesto',
            'specific_taxes' => 'Impuestos Específicos',
            'no_images' => 'No se ha subido ninguna imagen para este artículo.',
            'company' => 'Empresa',
            'item_type' => 'Tipo de Artículo',
            'product' => 'Producto',
            'service' => 'Servicio',
            'unit_measure' => 'Unidad de Medida',
            'is_active' => 'Activo',
            'manage_stock' => 'Controla Stock',
            'current_stock' => 'Stock Actual',
            'minimum_stock' => 'Stock Mínimo',
            'internal_id' => 'ID Interno',
            'created_at' => 'Creación',
            'updated_at' => 'Última Actualización',
            'deleted_at' => 'Eliminado el',
        ],
    ],
];

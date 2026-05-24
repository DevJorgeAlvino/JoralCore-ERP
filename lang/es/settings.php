<?php

return [
    'global_title' => 'Configuración Global del Sistema',
    'global_nav' => 'Configuración Global',
    'company_title' => 'Configuración de la Empresa',
    'company_nav' => 'Configuración',
    'system_group' => 'Sistema',
    
    'sections' => [
        'branding' => 'Branding del Sistema',
        'branding_desc' => 'Logo y nombre que se muestran en la pantalla de login del ERP.',
        'company_branding' => 'Branding de la Empresa',
        'company_branding_desc' => 'Logo, ícono y colores del panel para esta empresa.',
        'colors' => 'Colores del Panel',
        'colors_desc' => 'Colores principales del panel. Cada empresa configura sus propios colores.',
        'language' => 'Idioma de la Interfaz',
        'language_desc' => 'Selecciona el idioma preferido.',
        'business_hours' => 'Horarios de Atención',
        'business_hours_desc' => 'Configura los horarios de operación por día de la semana.',
        'currency' => 'Moneda Secundaria',
        'currency_desc' => 'Configura una moneda adicional y su tasa de cambio referencial.',
        'stock' => 'Alertas de Stock',
        'stock_desc' => 'Parámetros para notificaciones de inventario bajo.',
    ],
    
    'fields' => [
        'app_name' => 'Nombre de la Aplicación',
        'app_logo' => 'Logo Principal',
        'app_favicon' => 'Ícono / Favicon',
        'color_primary' => 'Color Primario',
        'color_secondary' => 'Color Secundario',
        'default_locale' => 'Idioma por Defecto',
        'locale' => 'Idioma de la Empresa',
        'day' => 'Día',
        'open' => 'Apertura',
        'close' => 'Cierre',
        'active' => 'Activo',
        'secondary_currency' => 'Moneda Secundaria',
        'exchange_rate' => 'Tasa de Cambio',
        'stock_alert_enabled' => 'Alertas habilitadas',
        'stock_alert_min' => 'Stock Mínimo',
    ],
    
    'helpers' => [
        'app_name' => 'Se muestra en la pestaña del navegador y en el login.',
        'logo_admin' => 'Recomendado: PNG transparente, 400×100px. Máx 2MB.',
        'favicon' => 'Recomendado: ICO o PNG de 32×32px. Máx 512KB.',
        'color_primary' => 'Color principal de botones y acentos.',
        'color_secondary' => 'Color de elementos secundarios.',
        'currency' => 'Moneda adicional para cotizaciones y reportes.',
        'exchange_rate' => 'Tasa de conversión referencial.',
        'stock_toggle' => 'Activa o desactiva las notificaciones de stock mínimo.',
        'stock_min' => 'Cantidad mínima de unidades antes de generar alerta.',
    ],
    
    'messages' => [
        'saved_global' => 'Los cambios globales se aplicarán en la próxima carga.',
        'saved_company' => 'Los cambios se aplicarán en la próxima carga del panel.',
        'saved_title' => 'Configuración guardada',
    ],

    'days' => [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miércoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sábado' => 'Sábado',
        'domingo' => 'Domingo',
    ]
];

<?php

return [
    'global_title' => 'Global System Settings',
    'global_nav' => 'Global Settings',
    'company_title' => 'Company Settings',
    'company_nav' => 'Settings',
    'system_group' => 'System',
    
    'sections' => [
        'branding' => 'System Branding',
        'branding_desc' => 'Logo and name shown on the ERP login screen.',
        'company_branding' => 'Company Branding',
        'company_branding_desc' => 'Logo, icon, and colors for this company panel.',
        'colors' => 'Panel Colors',
        'colors_desc' => 'Main panel colors. Each company configures its own colors.',
        'language' => 'Interface Language',
        'language_desc' => 'Select the preferred language.',
        'business_hours' => 'Business Hours',
        'business_hours_desc' => 'Configure operation hours per day of the week.',
        'currency' => 'Secondary Currency',
        'currency_desc' => 'Configure an additional currency and its reference exchange rate.',
        'stock' => 'Stock Alerts',
        'stock_desc' => 'Parameters for low inventory notifications.',
    ],
    
    'fields' => [
        'app_name' => 'Application Name',
        'app_logo' => 'Main Logo',
        'app_favicon' => 'Icon / Favicon',
        'color_primary' => 'Primary Color',
        'color_secondary' => 'Secondary Color',
        'default_locale' => 'Default Language',
        'locale' => 'Company Language',
        'day' => 'Day',
        'open' => 'Opening Time',
        'close' => 'Closing Time',
        'active' => 'Active',
        'secondary_currency' => 'Secondary Currency',
        'exchange_rate' => 'Exchange Rate',
        'stock_alert_enabled' => 'Alerts Enabled',
        'stock_alert_min' => 'Minimum Stock',
    ],
    
    'helpers' => [
        'app_name' => 'Shown in the browser tab and login screen.',
        'logo_admin' => 'Recommended: transparent PNG, 400×100px. Max 2MB.',
        'favicon' => 'Recommended: 32×32px ICO or PNG. Max 512KB.',
        'color_primary' => 'Main color for buttons and accents.',
        'color_secondary' => 'Color for secondary elements.',
        'currency' => 'Additional currency for quotes and reports.',
        'exchange_rate' => 'Reference conversion rate.',
        'stock_toggle' => 'Enables or disables minimum stock notifications.',
        'stock_min' => 'Minimum quantity of units before generating an alert.',
    ],
    
    'messages' => [
        'saved_global' => 'Global changes will be applied on the next load.',
        'saved_company' => 'Changes will be applied on the next panel load.',
        'saved_title' => 'Settings saved',
    ],

    'days' => [
        'lunes' => 'Monday',
        'martes' => 'Tuesday',
        'miércoles' => 'Wednesday',
        'jueves' => 'Thursday',
        'viernes' => 'Friday',
        'sábado' => 'Saturday',
        'domingo' => 'Sunday',
    ]
];

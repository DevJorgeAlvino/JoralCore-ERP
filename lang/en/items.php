<?php

return [
    'title' => 'Item Master',
    'single' => 'Item',

    'table' => [
        'item' => 'Item',
        'stock' => 'Stock',
        'type' => 'Type',
    ],

    'actions' => [
        'import' => 'Import Items',
        'import_desc' => 'The system natively processes and maps CSV files to ensure optimal performance. If you use Excel, save your file as .csv before uploading.<br><br><strong><span style="color: #eab308;">⚠️ Maximum limit: 10,000 records per file.</span></strong>',
    ],

    'infolist' => [
        'sections' => [
            'main_info' => 'Main Information',
            'main_info_desc' => 'Basic identification and description data of the item.',
            'prices_taxes' => 'Prices & Taxes',
            'prices_taxes_desc' => 'Cost configuration, sale prices and tax details.',
            'gallery' => 'Image Gallery',
            'gallery_desc' => 'Photos and visual material of the item.',
            'organization' => 'Organization',
            'organization_desc' => 'Classification and availability in the system.',
            'inventory' => 'Inventory',
            'inventory_desc' => 'Current stock management and minimum levels for alerts.',
            'metadata' => 'Metadata',
            'metadata_desc' => 'Audit log.',
        ],
        'fields' => [
            'name' => 'Name',
            'sku' => 'SKU',
            'barcode' => 'Barcode',
            'slug' => 'Slug',
            'description' => 'Description',
            'purchase_cost' => 'Purchase Cost',
            'sale_price' => 'Sale Price',
            'tax_type' => 'Tax Type',
            'specific_taxes' => 'Specific Taxes',
            'no_images' => 'No image has been uploaded for this item.',
            'company' => 'Company',
            'item_type' => 'Item Type',
            'product' => 'Product',
            'service' => 'Service',
            'unit_measure' => 'Unit of Measure',
            'is_active' => 'Active',
            'manage_stock' => 'Manage Stock',
            'current_stock' => 'Current Stock',
            'minimum_stock' => 'Minimum Stock',
            'internal_id' => 'Internal ID',
            'created_at' => 'Created At',
            'updated_at' => 'Last Updated',
            'deleted_at' => 'Deleted At',
        ],
    ],
];

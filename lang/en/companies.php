<?php

return [
    'title' => 'Companies',
    'single' => 'Company',

    'sections' => [
        'commercial' => 'Commercial Information',
        'commercial_desc' => 'Main and public company details.',
        'localization' => 'Localization and Regional Settings',
        'legal' => 'Legal Information (Optional)',
        'legal_desc' => 'Legal name, Tax ID, and formal fiscal data.',
        'contact' => 'Address and Contact (Optional)',
        'contact_desc' => 'Physical location and official communication channels.',
    ],

    'fields' => [
        'name' => 'Commercial Name',
        'slug' => 'Slug (Identifier)',
        'description' => 'Description',
        'country' => 'Country',
        'currency' => 'Currency',
        'timezone' => 'Timezone',
        'legal_name' => 'Legal Name',
        'dv' => 'Verification Digit',
        'economic_activity_code' => 'Economic Activity',
        'tax_address' => 'Tax Address',
        'phone' => 'Phone',
        'email' => 'Email',
        'created_at' => 'Created At',
        'updated_at' => 'Last Updated',
        'deleted_at' => 'Deleted At',
    ],

    'table' => [
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'relations' => [
        'assigned' => 'Assigned Companies',
        'assign_company' => 'Assign Company',
        'assigned_users' => 'Assigned Users',
        'attach_user' => 'Attach User',
        'create_user' => 'Create User',
        'roles' => 'Company Roles',
        'create_role' => 'Create Role',
    ],
];

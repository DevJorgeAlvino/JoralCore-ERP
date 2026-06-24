<?php

return [
    'title' => 'Empresas',
    'single' => 'Empresa',

    'sections' => [
        'commercial' => 'Información Comercial',
        'commercial_desc' => 'Datos principales y públicos de la empresa.',
        'localization' => 'Localización y Configuración Regional',
        'legal' => 'Información Legal (Opcional)',
        'legal_desc' => 'Razón social, NIT/RUT y datos fiscales formales.',
        'contact' => 'Dirección y Contacto (Opcional)',
        'contact_desc' => 'Ubicación física y medios de comunicación oficiales.',
    ],

    'fields' => [
        'name' => 'Nombre Comercial',
        'slug' => 'Slug (Identificador)',
        'description' => 'Descripción',
        'country' => 'País',
        'currency' => 'Moneda',
        'timezone' => 'Zona Horaria',
        'legal_name' => 'Razón Social',
        'dv' => 'Dígito Verificador',
        'economic_activity_code' => 'Actividad Económica',
        'tax_address' => 'Dirección Fiscal',
        'phone' => 'Teléfono',
        'email' => 'Correo Electrónico',
        'created_at' => 'Fecha de Creación',
        'updated_at' => 'Última Actualización',
        'deleted_at' => 'Fecha de Eliminación',
    ],

    'table' => [
        'status' => 'Estado',
        'active' => 'Activa',
        'inactive' => 'Inactiva',
    ],

    'relations' => [
        'assigned' => 'Empresas Asignadas',
        'assign_company' => 'Asignar Empresa',
        'assigned_users' => 'Usuarios Asignados',
        'attach_user' => 'Vincular Usuario',
        'create_user' => 'Crear Usuario',
        'roles' => 'Roles de la Empresa',
        'create_role' => 'Crear Rol',
    ],
];

<?php

return [
    'title' => 'Usuarios',
    'single' => 'Usuario',

    'fields' => [
        'name' => 'Nombre',
        'email' => 'Correo Electrónico',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar Contraseña',
    ],

    'widgets' => [
        'stats' => [
            'admin_total' => 'Total Usuarios',
            'admin_total_desc' => 'Usuarios registrados en el sistema',
            'company_total' => 'Personal',
            'company_total_desc' => 'Empleados en la empresa',
            'verified' => 'Verificados',
            'verified_desc' => 'Accesos confirmados',
            'pending' => 'Pendientes',
            'pending_desc' => 'Falta confirmación',
        ],
    ],
    'form' => [
        'personal_info' => 'Información Personal',
        'personal_info_desc' => 'Datos básicos del usuario.',
        'security' => 'Seguridad',
        'security_desc' => 'Gestión de contraseñas de la cuenta.',
    ],
    'table' => [
        'user' => 'Usuario',
        'roles' => 'Roles',
        'global_roles' => 'Roles Globales',
        'assigned_companies' => 'Empresas Asignadas',
        'verified' => 'Verificado',
        'registered' => 'Registro',
        'yes' => 'Sí',
        'no' => 'No',
    ],
];

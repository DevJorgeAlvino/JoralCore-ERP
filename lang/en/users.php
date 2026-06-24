<?php

return [
    'title' => 'Users',
    'single' => 'User',

    'fields' => [
        'name' => 'Name',
        'email' => 'Email Address',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
    ],

    'widgets' => [
        'stats' => [
            'admin_total' => 'Total Users',
            'admin_total_desc' => 'Users registered in the system',
            'company_total' => 'Staff',
            'company_total_desc' => 'Company employees',
            'verified' => 'Verified',
            'verified_desc' => 'Confirmed access',
            'pending' => 'Pending',
            'pending_desc' => 'Confirmation pending',
        ],
    ],
    'form' => [
        'personal_info' => 'Personal Information',
        'personal_info_desc' => 'Basic user data.',
        'security' => 'Security',
        'security_desc' => 'Account password management.',
    ],
    'table' => [
        'user' => 'User',
        'roles' => 'Roles',
        'global_roles' => 'Global Roles',
        'assigned_companies' => 'Assigned Companies',
        'verified' => 'Verified',
        'registered' => 'Registered',
        'yes' => 'Yes',
        'no' => 'No',
    ],
];

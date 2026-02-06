<?php

return [
    'title' => 'System Settings',
    'subtitle' => 'Manage company information',

    'categories' => [
        'company_info' => [
            'title' => 'Company Information',
            'description' => 'Update company details',
        ],
    ],

    'company_info' => [
        'company_name' => [
            'name' => 'Company Name',
            'description' => 'Official company name',
            'unit' => '',
            'help' => 'Enter the name as it should appear on documents',
        ],
        'address' => [
            'name' => 'Address',
            'description' => 'Company address',
            'unit' => '',
            'help' => 'Company address details',
        ],
        'field' => [
            'name' => 'Field/Activity',
            'description' => 'Business field or activity',
            'unit' => '',
            'help' => 'Short description of the company activity',
        ],
    ],

    'common' => [
        'save' => 'Save settings',
        'cancel' => 'Cancel',
        'edit' => 'Edit setting',
        'update' => 'Update setting',
        'delete' => 'Delete setting',
        'confirm_delete' => 'Are you sure you want to delete this setting?',
        'setting_updated' => 'Setting updated successfully',
        'setting_deleted' => 'Setting deleted successfully',
        'validation_error' => 'Please check input values',
        'no_settings' => 'No settings found',
        'loading' => 'Loading settings...',
        'refresh' => 'Refresh settings',
        'export' => 'Export settings',
        'import' => 'Import settings',
        'current_value' => 'Current value',
        'unit' => 'Unit',
        'help' => 'Help',
        'new_value' => 'New value',
        'disabled' => 'Disabled',
        'enabled' => 'Enabled',
    ],

    'form' => [
        'setting_key' => 'Setting key',
        'setting_value' => 'Setting value',
        'setting_trans_key' => 'Translation key',
        'category' => 'Category',
        'description' => 'Description',
        'validation_rules' => 'Validation rules',
        'is_active' => 'Active',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
    ],

    'validation' => [],
];

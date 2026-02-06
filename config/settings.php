<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Settings Validation Rules (Company Info)
    |--------------------------------------------------------------------------
    */
    'validation' => [
        // group: company_info
        // company_name, address, field
        'invoice_default_language' => [
            'rules' => [
                'value' => 'required|in:ar,en,fr',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values (Company Info)
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        // company_info defaults left empty on purpose
    ],
];

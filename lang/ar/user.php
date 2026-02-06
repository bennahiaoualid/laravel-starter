<?php

return [

    'profile' => [
        'information' => 'profile information',
        'title' => 'User Profile',
        'yours' => 'your profile',
        'name' => 'Name',
        'email' => 'email',
        'birthdate' => 'birthdate',
        'age' => 'age',
        'password' => [
            'password' => 'password',
            'update' => 'update password',
            'new' => 'new password',
            'current' => 'current password',
            'confirm' => 'confirm password',
            '8_at_least' => 'contains 8 characters at least',
            'contains_number' => 'contains number',
            'contains_upper' => 'contains upper case character',
            'contains_symbol' => 'contains symbol character',
        ],
        'genders' => [
            'gender' => 'gender',
            'male' => 'male',
            'female' => 'female',
        ],
        'status' => [
            'state' => 'state',
            'active' => 'verified',
            'inactive' => 'not verified',
        ],
        'verify' => [
            'confirm' => 'Confirm Email',
            'unverified' => 'Your email address is unverified.',
            're-send-email' => 'Click here to re-send the verification email.',
            'email-sent' => 'A new verification link has been sent to your email address.',
        ],
        'actions' => [
            'created_by' => 'created by',
        ],
    ],
    'two_factor' => [
        'title' => 'Two-Factor Authentication',
        'description' => 'Add an extra layer of protection to your account using Google Authenticator.',
        'enabled' => 'Enabled',
        'setup_instructions' => 'Click the button below to start the setup process. You will scan a QR code and confirm a 6-digit code from Google Authenticator.',
        'enable_button' => 'Enable 2FA',
        'scan_qr' => 'Scan this QR code using Google Authenticator:',
        'qr_alt' => 'Two-factor QR code',
        'qr_error' => 'Unable to render the QR code. Cancel and try generating a new secret.',
        'show_manual_code' => 'Show manual code',
        'hide_manual_code' => 'Hide manual code',
        'enter_code' => 'Enter the 6-digit code to confirm',
        'verify_enable' => 'Verify & Enable',
        'cancel_setup' => 'Cancel Setup',
        'active_message' => 'Two-factor authentication is active. You will be asked for a 6-digit code from Google Authenticator after signing in.',
        'disable_button' => 'Disable 2FA',
        'challenge' => [
            'title' => 'Two-Factor Verification',
            'description' => 'Enter the 6-digit code from your authenticator app to finish signing in.',
            'code_label' => 'Verification Code',
            'verify_button' => 'Verify & Continue',
            'lost_access' => 'Lost access to your authenticator app? Contact an administrator to reset 2FA on your account.',
        ],
        'errors' => [
            'no_secret' => 'No two-factor secret exists for your account. Please start setup again.',
            'cannot_read_secret' => 'Unable to read your two-factor secret. Please generate a new one.',
            'invalid_code' => 'The provided verification code is invalid.',
            'cannot_verify_account' => 'Unable to verify your account. Please contact support.',
        ],
    ],
    'show_inactive' => 'Show Inactive Users',

];

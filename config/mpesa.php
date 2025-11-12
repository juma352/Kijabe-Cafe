<?php

return [
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'environment' => env('MPESA_ENV', 'sandbox'), // 'sandbox' or 'live'
    'shortcode' => env('MPESA_SHORTCODE'),
    'passkey' => env('MPESA_PASSKEY'),
    'callback_url' => env('MPESA_CALLBACK_URL'),
    'confirmation_url' => env('MPESA_CONFIRMATION_URL'),
    'validation_url' => env('MPESA_VALIDATION_URL'),
    'account_reference' => env('MPESA_ACCOUNT_REFERENCE', 'KijabeHospital'),
    'transaction_desc' => env('MPESA_TRANSACTION_DESC', 'Hospital Payment'),
];
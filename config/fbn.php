<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Per Page Result Set
    |--------------------------------------------------------------------------
    |
    | The default number of items return in pagination results
    |
    */

    'per_page' => 20,

    /*
    |--------------------------------------------------------------------------
    | Application Country Timezone
    |--------------------------------------------------------------------------
    |
    | The timezone of the country of the application
    |
    */

    'country_timezone' => 'Africa/Lagos',

    /*
    |--------------------------------------------------------------------------
    | Date Query Timezone
    |--------------------------------------------------------------------------
    |
    | The timezone to use for date queries
    |
    */

    'date_query_timezone' => 'Atlantic/Cape_Verde',

    /*
    |--------------------------------------------------------------------------
    | Email Verification Hash
    |--------------------------------------------------------------------------
    |
    | The hash to use for email verification
    |
    */

    'email_verification_hash' => env('EMAIL_VERIFICATION_HASH'),

    /*
    |--------------------------------------------------------------------------
    | Reset Password Verification Hash
    |--------------------------------------------------------------------------
    |
    | The reset password verification hash
    |
    */

    'reset_password_verification_hash' => env('RESET_PASSWORD_VERIFICATION_HASH'),

    /*
    |--------------------------------------------------------------------------
    | Password Encryption Key
    |--------------------------------------------------------------------------
    |
    | The key used for password encryption
    |
    */

    'password_encryption_key' => env('PASSWORD_ENCRYPTION_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Password Encryption Initialization Key
    |--------------------------------------------------------------------------
    |
    | The initialization vector used for password encryption
    |
    */

    'password_encryption_initialization_vector' => env('PASSWORD_ENCRYPTION_INITIALIZATION_VECTOR'),

    /*
    |--------------------------------------------------------------------------
    | User Verification Expiration Time
    |--------------------------------------------------------------------------
    |
    | The number of seconds a user verification expires
    |
    */

    'verification_expiration_time' => 1800,

    /*
    |--------------------------------------------------------------------------
    | Reset Password Verification Expiration Time
    |--------------------------------------------------------------------------
    |
    | The number of seconds a user verification expires
    |
    */

    'reset_password_verification_expiration_time' => 1800,

];

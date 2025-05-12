<?php

return [
    /**
     * You can select either uat or sit for testing, and for production, use prod. 
     * Please only include one of these environments (uat, sit, prod) in the environment configuration field.
     */
    'environment' => 'uat',
    /**
     * Merchant ID
     */
    'merchant_id' => '116194',
    /**
     * Terminal ID
     */
    'terminal_id' => '708393',
    /**
     * Secret Key
     */
    'secret_key' => '2B03FCDC101D3F160744342BFBA0BEA0E835EE436B6A985BA30464418392C703',
    /**
     * Callback URL
     * Replace only the {example.com} with your site domain
     */
    // 'callback_url' => 'https://{YourWebsiteURL}/amwalpay/callback',
    'callback_url' => 'http://127.0.0.1:8000/amwalpay/callback',

];

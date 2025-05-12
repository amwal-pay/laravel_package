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
    'merchant_id' => 'xxxxx',
    /**
     * Terminal ID
     */
    'terminal_id' => 'xxxxxx',
    /**
     * Secret Key
     */
    'secret_key' => 'xxxxxxxxxxxxxxxxx',
    /**
     * Callback URL
     * Replace only the {example.com} with your site domain
     */
     'callback_url' => 'https://{YourWebsiteURL}/amwalpay/callback',

];

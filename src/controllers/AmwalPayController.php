<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AmwalPayController extends Controller
{
    protected $environment;
    protected $merchant_id;
    protected $terminal_id;
    protected $secret_key;
    protected $callback_url;

    /**
     * Initialize AmwalPay Configuration from config file.
     */
    public function __construct()
    {
        $this->environment = config('amwalpay.environment');
        $this->merchant_id = config('amwalpay.merchant_id');
        $this->terminal_id = config('amwalpay.terminal_id');
        $this->secret_key = config('amwalpay.secret_key');
        $this->callback_url = config('amwalpay.callback_url');
    }

    /**
     * Handle the payment process view rendering.
     * Prepares the data and secure hash to send to the Amwal SmartBox.
     */
    public function process()
    {
        try {
            // Prepare static or dynamic order data
            $orderId = '1';       // Replace with dynamic ID logic in production
            $amount = '1.000';   // Replace with real amount logic

            // Language code handling
            $locale = app()->getLocale();
            $locale = (strpos($locale, 'en') !== false) ? 'en' : 'ar';

            $datetime = date('YmdHis');

            // Generate secure hash for request integrity
            $secret_key = self::generateString(
                $amount,
                512,
                $this->merchant_id,
                $orderId,
                $this->terminal_id,
                $this->secret_key,
                $datetime
            );

            // Construct the payload for SmartBox
            $data = (object) [
                'AmountTrxn' => $amount,
                'MerchantReference' => $orderId,
                'MID' => $this->merchant_id,
                'TID' => $this->terminal_id,
                'CurrencyId' => 512,
                'LanguageId' => $locale,
                'SecureHash' => $secret_key,
                'TrxDateTime' => $datetime,
                'PaymentViewType' => 1,
                'RequestSource' => 'Checkout_Direct_Integration',
                'SessionToken' => '',
            ];

            $jsonData = json_encode($data);
            $url = $this->getSmartBoxUrl($this->environment);
            $callback = $this->callback_url;
            Log::info('Initiating AmwalPay payment process', [
                'data' => $data,
                'url' => $url,
                'callback' => $callback,
            ]);
            // Return the SmartBox payment view
            return view('amwalpay::smartbox', compact('jsonData', 'url', 'callback'));

        } catch (Exception $e) {
            // Log or handle the exception
            Log::error('AmwalPay process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response = ['IsSuccess' => 'false', 'Message' => $e->getMessage()];
            return response()->json($response);
        }
    }

    /**
     * Handle the callback from Amwal after payment attempt.
     * Verifies the transaction integrity and returns success or failure.
     */
    public function callBack(Request $request)
    {
        try {

            $orderId = self::sanitizeVar('merchantReference');
            if (!$orderId) {
                return response()->json([
                    'IsSuccess' => 'false',
                    'Message' => 'Ops, you are accessing wrong data'
                ]);
            }

            $isPaymentApproved = false;

            // Prepare data for integrity hash check
            $integrityParameters = [
                "amount" => self::sanitizeVar('amount'),
                "currencyId" => self::sanitizeVar('currencyId'),
                "customerId" => self::sanitizeVar('customerId'),
                "customerTokenId" => self::sanitizeVar('customerTokenId'),
                "merchantId" => $this->merchant_id,
                "merchantReference" => self::sanitizeVar('merchantReference'),
                "responseCode" => self::sanitizeVar('responseCode'),
                "terminalId" => $this->terminal_id,
                "transactionId" => self::sanitizeVar('transactionId'),
                "transactionTime" => self::sanitizeVar('transactionTime')
            ];

            $secureHashValue = self::generateStringForFilter($integrityParameters, $this->secret_key);

            $integrityParameters['secureHashValue'] = $secureHashValue;
            $integrityParameters['secureHashValueOld'] = self::sanitizeVar('secureHashValue');
            Log::info('AmwalPay callback received', [
                'request' => $integrityParameters
            ]);
            // Check payment status based on response code or secure hash match
            if (self::sanitizeVar('responseCode') === '00' || $secureHashValue === self::sanitizeVar('secureHashValue')) {
                $isPaymentApproved = true;
            }
            // You can adjust the redirection on success or failure transactions according to your needs
            if ($isPaymentApproved) {
                $response = ['IsSuccess' => 'true', 'Message' => 'AmwalPay : Payment Approved'];
            } else {
                $response = ['IsSuccess' => 'false', 'Message' => 'AmwalPay : Payment is not completed'];
            }

        } catch (Exception $e) {
            $response = ['IsSuccess' => 'false', 'Message' => $e->getMessage()];
        }

        return response()->json($response);
    }

    /**
     * Get SmartBox JavaScript URL based on environment.
     */
    public function getSmartBoxUrl($env)
    {
        if ($env === "prod") {
            return "https://checkout.amwalpg.com/js/SmartBox.js?v=1.1";
        } elseif ($env === "uat") {
            return "https://test.amwalpg.com:7443/js/SmartBox.js?v=1.1";
        } elseif ($env === "sit") {
            return "https://test.amwalpg.com:19443/js/SmartBox.js?v=1.1";
        }

        return null;
    }
    /**
     * Generate secure hash string for SmartBox payment initiation.
     */
    public static function generateString(
        $amount,
        $currencyId,
        $merchantId,
        $merchantReference,
        $terminalId,
        $hmacKey,
        $trxDateTime
    ) {

        $string = "Amount={$amount}&CurrencyId={$currencyId}&MerchantId={$merchantId}&MerchantReference={$merchantReference}&RequestDateTime={$trxDateTime}&SessionToken=&TerminalId={$terminalId}";

        $sign = self::encryptWithSHA256($string, $hmacKey);
        return strtoupper($sign);
    }
    /**
     * Generate HMAC-SHA256 hash using hex-encoded key.
     */
    public static function encryptWithSHA256($input, $hexKey)
    {
        // Convert the hex key to binary
        $binaryKey = hex2bin($hexKey);
        // Calculate the SHA-256 hash using hash_hmac
        $hash = hash_hmac('sha256', $input, $binaryKey);
        return $hash;
    }
    /**
     * Generate secure hash from a key-value array of payment data.
     */
    public static function generateStringForFilter(
        $data,
        $hmacKey

    ) {
        // Convert data array to string key value with and sign
        $string = '';
        foreach ($data as $key => $value) {
            $string .= $key . '=' . ($value === "null" || $value === "undefined" ? '' : $value) . '&';
        }
        $string = rtrim($string, '&');
        // Generate SIGN
        $sign = self::encryptWithSHA256($string, $hmacKey);
        return strtoupper($sign);
    }
    /**
     * Safely retrieve a GET or POST variable, sanitized.
     */
    public static function sanitizeVar($name, $global = 'GET')
    {
        if (isset($GLOBALS['_' . $global][$name])) {
            if (is_array($GLOBALS['_' . $global][$name])) {
                return $GLOBALS['_' . $global][$name];
            }
            return htmlspecialchars($GLOBALS['_' . $global][$name], ENT_QUOTES);
        }
        return null;
    }
}

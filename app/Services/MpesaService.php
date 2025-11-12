<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MpesaService
{
    private $consumerKey;
    private $consumerSecret;
    private $environment;
    private $shortcode;
    private $passkey;
    private $callbackUrl;
    private $validationUrl;
    private $confirmationUrl;
    private $baseUrl;

    public function __construct()
    {
        $this->consumerKey = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->environment = config('mpesa.environment');
        $this->shortcode = config('mpesa.shortcode');
        $this->passkey = config('mpesa.passkey');
        $this->callbackUrl = config('mpesa.callback_url');
        $this->validationUrl = config('mpesa.validation_url');
        $this->confirmationUrl = config('mpesa.confirmation_url');
        
        // Set base URL based on environment
        $this->baseUrl = $this->environment === 'live' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * Get OAuth access token
     */
    public function getAccessToken()
    {
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        
        $url = $this->environment === 'live' 
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        Log::info('Attempting M-Pesa token request', [
            'url' => $url,
            'environment' => $this->environment,
            'consumer_key' => substr($this->consumerKey, 0, 10) . '...',
            'shortcode' => $this->shortcode
        ]);

        try {
            $response = Http::timeout(30)->connectTimeout(10)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for testing
                ])
                ->withHeaders([
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                ])->get($url);

            Log::info('M-Pesa token response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    return $data['access_token'];
                } else {
                    Log::error('No access_token in response', ['response' => $data]);
                    return null;
                }
            }

            Log::error('Failed to get M-Pesa access token', [
                'response_body' => $response->body(),
                'status' => $response->status(),
                'headers' => $response->headers()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa access token exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $url
            ]);
            return null;
        }
    }

    /**
     * Generate payment reference for Lipa na M-Pesa
     */
    public function generatePaymentReference($saleId)
    {
        // Create a unique reference using sale ID and timestamp
        return 'SALE' . str_pad($saleId, 6, '0', STR_PAD_LEFT) . substr(time(), -4);
    }

    /**
     * Get payment instructions for traditional Lipa na M-Pesa
     */
    public function getLipaNaMpesaInstructions($paymentReference, $amount)
    {
        return [
            'paybill' => $this->shortcode,
            'account_number' => $paymentReference,
            'amount' => $amount,
            'instructions' => [
                '1. Go to M-Pesa on your phone',
                '2. Select Lipa na M-Pesa',
                '3. Select Pay Bill',
                '4. Enter Business No: ' . $this->shortcode,
                '5. Enter Account No: ' . $paymentReference,
                '6. Enter Amount: KSh ' . number_format($amount),
                '7. Enter your M-Pesa PIN',
                '8. Confirm the payment'
            ]
        ];
    }

    /**
     * Initiate STK Push (Lipa na M-Pesa Online) - kept for compatibility
     */
    public function stkPush($phoneNumber, $amount, $accountReference = 'KijabeHospital', $transactionDesc = 'Hospital Payment')
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Format phone number
        $phone = $this->formatPhoneNumber($phoneNumber);

        $url = $this->environment === 'live'
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $requestData = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc
        ];

        try {
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for testing
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($url, $requestData);

            $responseData = $response->json();

            Log::info('STK Push Response', [
                'request' => $requestData,
                'response' => $responseData
            ]);

            if ($response->successful() && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'message' => 'STK Push sent successfully',
                    'checkoutRequestId' => $responseData['CheckoutRequestID'],
                    'merchantRequestId' => $responseData['MerchantRequestID'],
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['errorMessage'] ?? $responseData['ResponseDescription'] ?? 'STK Push failed',
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('STK Push Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query STK Push status
     */
    public function stkQuery($checkoutRequestId)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $url = $this->environment === 'live'
            ? 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';

        $requestData = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $requestData);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('STK Query Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Query failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * C2B Register URLs
     */
    public function registerUrls()
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = $this->environment === 'live'
            ? 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl'
            : 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

        $requestData = [
            'ShortCode' => $this->shortcode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $this->confirmationUrl,
            'ValidationURL' => $this->validationUrl
        ];

        try {
            Log::info('Registering C2B URLs with request data:', $requestData);

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for testing
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($url, $requestData);

            Log::info('M-Pesa register URL response:', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Register URLs Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to required format
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any spaces, hyphens, or plus signs
        $phone = preg_replace('/[\s\-\+]/', '', $phoneNumber);
        
        // If starts with 0, replace with 254
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // If doesn't start with 254, add it
        if (substr($phone, 0, 3) !== '254') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }

    /**
     * Test connection to M-Pesa API
     */
    public function testConnection()
    {
        Log::info('Testing M-Pesa connection', [
            'environment' => $this->environment,
            'consumer_key' => substr($this->consumerKey, 0, 10) . '...',
            'shortcode' => $this->shortcode,
            'base_url' => $this->baseUrl
        ]);

        $token = $this->getAccessToken();
        
        if ($token) {
            return [
                'success' => true,
                'message' => 'Successfully connected to M-Pesa API',
                'environment' => $this->environment,
                'shortcode' => $this->shortcode,
                'token_preview' => substr($token, 0, 20) . '...'
            ];
        }
        
        // Check logs for more specific error
        $logPath = storage_path('logs/laravel.log');
        $lastError = 'Unknown error - check application logs';
        
        if (file_exists($logPath)) {
            $logs = file_get_contents($logPath);
            if (strpos($logs, 'M-Pesa access token exception') !== false) {
                $lastError = 'Network connection error - check internet connection';
            } elseif (strpos($logs, 'Failed to get M-Pesa access token') !== false) {
                $lastError = 'Invalid credentials or API response error';
            }
        }

        return [
            'success' => false,
            'message' => 'Failed to connect to M-Pesa API',
            'error' => $lastError,
            'environment' => $this->environment,
            'shortcode' => $this->shortcode,
            'troubleshooting' => [
                'Check internet connection',
                'Verify M-Pesa credentials in .env file',
                'Ensure sandbox environment is accessible',
                'Check application logs for detailed errors'
            ]
        ];
    }

    /**
     * Query account balance
     */
    public function queryAccountBalance()
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get access token'
                ];
            }

            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $requestData = [
                'Initiator' => 'testapi',  // For sandbox
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'AccountBalance',
                'PartyA' => $this->shortcode,
                'IdentifierType' => '4',
                'Remarks' => 'Account balance query',
                'QueueTimeOutURL' => $this->callbackUrl . '/balance/timeout',
                'ResultURL' => $this->callbackUrl . '/balance/result'
            ];

            $response = Http::withToken($accessToken)
                ->withOptions(['verify' => false])
                ->post($this->baseUrl . '/mpesa/accountbalance/v1/query', $requestData);

            Log::info('Account balance query response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'raw_response' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Account balance query failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Account balance query failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query transaction status
     */
    public function queryTransactionStatus($transactionId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get access token'
                ];
            }

            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

            $requestData = [
                'Initiator' => 'testapi',  // For sandbox
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'TransactionStatusQuery',
                'TransactionID' => $transactionId,
                'PartyA' => $this->shortcode,
                'IdentifierType' => '4',
                'ResultURL' => $this->callbackUrl . '/transaction/result',
                'QueueTimeOutURL' => $this->callbackUrl . '/transaction/timeout',
                'Remarks' => 'Transaction status query',
                'Occasion' => 'Transaction inquiry'
            ];

            $response = Http::withToken($accessToken)
                ->withOptions(['verify' => false])
                ->post($this->baseUrl . '/mpesa/transactionstatus/v1/query', $requestData);

            Log::info('Transaction status query response', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'raw_response' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Transaction status query failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Transaction status query failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate security credential for API requests
     */
    private function generateSecurityCredential()
    {
        // For sandbox, use the test credential
        // In production, you would encrypt with the Safaricom public key
        return 'Safaricom999!*!';
    }

    /**
     * Parse transaction history from callback responses
     */
    public function parseTransactionHistory($callbackData)
    {
        try {
            // Parse the Result parameter which contains transaction details
            if (isset($callbackData['Result']) && isset($callbackData['Result']['ResultParameters'])) {
                $parameters = $callbackData['Result']['ResultParameters']['ResultParameter'];
                
                $transactionData = [];
                foreach ($parameters as $param) {
                    $transactionData[$param['Key']] = $param['Value'] ?? null;
                }

                return [
                    'success' => true,
                    'transaction' => $transactionData
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid callback data format'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to parse transaction history', [
                'error' => $e->getMessage(),
                'data' => $callbackData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to parse transaction data'
            ];
        }
    }

    /**
     * Register C2B URLs to automatically receive paybill payments
     */
    public function registerC2BUrls()
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get access token'
                ];
            }

            $requestData = [
                'ShortCode' => $this->shortcode,
                'ResponseType' => 'Completed',  // or 'Cancelled'
                'ConfirmationURL' => $this->confirmationUrl,
                'ValidationURL' => $this->validationUrl
            ];

            Log::info('Registering C2B URLs with request data:', $requestData);

            $response = Http::withToken($accessToken)
                ->timeout(30)->connectTimeout(10)
                ->withOptions(['verify' => false])
                ->post($this->baseUrl . '/mpesa/c2b/v1/registerurl', $requestData);

            Log::info('C2B URL registration response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'C2B URLs registered successfully',
                    'data' => $responseData,
                    'urls_registered' => [
                        'confirmation' => $this->confirmationUrl,
                        'validation' => $this->validationUrl
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to register C2B URLs',
                    'error' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('C2B URL registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'C2B URL registration failed: ' . $e->getMessage()
            ];
        }
    }
}
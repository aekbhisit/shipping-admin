<?php

namespace Modules\Shipper\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * JTExpressApiService
 * Purpose: Integration with J&T Express API
 * Authentication: API Key + Secret
 * Services: Standard, Express, Premium
 */
class JTExpressApiService
{
    protected $baseUrl = 'https://openapi.jtexpress.my';
    
    /**
     * Get quote from J&T Express API
     * Credentials Required: api_key, api_secret, customer_code
     */
    public function getQuote(array $credentials, array $packageData): array
    {
        try {
            // Validate required credentials
            if (!isset($credentials['api_key']) || !isset($credentials['api_secret']) || !isset($credentials['customer_code'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required credentials: api_key, api_secret, customer_code'
                ];
            }

            // Prepare request data
            $requestData = $this->prepareRequestData($packageData, $credentials);
            
            // Generate signature for authentication
            $signature = $this->generateSignature($requestData, $credentials['api_secret']);
            
            // Make API call
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key' => $credentials['api_key'],
                    'X-Signature' => $signature
                ])
                ->post($this->baseUrl . '/api/v1/freight/calculate', $requestData);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return [
                        'success' => true,
                        'quote_price' => $data['data']['freight'] ?? 0,
                        'service_type' => $data['data']['service_type'] ?? 'Standard',
                        'estimated_days' => $data['data']['estimated_days'] ?? 2,
                        'reference' => $data['data']['order_id'] ?? null
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $data['message'] ?? 'Unknown API error'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'API request failed: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('J&T Express API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare request data for J&T Express API
     */
    protected function prepareRequestData(array $packageData, array $credentials): array
    {
        return [
            'customer_code' => $credentials['customer_code'],
            'package_info' => [
                'weight' => $packageData['package']['weight'] ?? 1.0,
                'length' => $packageData['package']['length'] ?? 10,
                'width' => $packageData['package']['width'] ?? 10,
                'height' => $packageData['package']['height'] ?? 10,
            ],
            'sender_info' => [
                'postcode' => $packageData['pickup']['postcode'] ?? '10110'
            ],
            'receiver_info' => [
                'postcode' => $packageData['delivery']['postcode'] ?? '10120'
            ],
            'service_type' => $packageData['service_type'] ?? 'STANDARD',
            'timestamp' => time()
        ];
    }

    /**
     * Generate signature for J&T Express authentication
     */
    protected function generateSignature(array $data, string $secret): string
    {
        $dataString = json_encode($data, JSON_UNESCAPED_SLASHES);
        return hash_hmac('sha256', $dataString, $secret);
    }

    /**
     * Test connection to J&T Express API
     */
    public function testConnection(array $credentials): bool
    {
        try {
            $testData = [
                'package' => [
                    'weight' => 1.0,
                    'length' => 10,
                    'width' => 10,
                    'height' => 10
                ],
                'pickup' => ['postcode' => '10110'],
                'delivery' => ['postcode' => '10120']
            ];

            $result = $this->getQuote($credentials, $testData);
            return $result['success'];

        } catch (\Exception $e) {
            Log::error('J&T Express connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get supported services
     */
    public function getSupportedServices(): array
    {
        return [
            'STANDARD' => 'Standard Delivery',
            'EXPRESS' => 'Express Delivery',
            'PREMIUM' => 'Premium Service'
        ];
    }

    /**
     * Validate credentials format
     */
    public function validateCredentials(array $credentials): array
    {
        $errors = [];

        if (!isset($credentials['api_key']) || empty($credentials['api_key'])) {
            $errors[] = 'API Key is required';
        }

        if (!isset($credentials['api_secret']) || empty($credentials['api_secret'])) {
            $errors[] = 'API Secret is required';
        }

        if (!isset($credentials['customer_code']) || empty($credentials['customer_code'])) {
            $errors[] = 'Customer Code is required';
        }

        return $errors;
    }
} 
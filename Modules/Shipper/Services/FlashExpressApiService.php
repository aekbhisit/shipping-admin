<?php

namespace Modules\Shipper\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * FlashExpressApiService
 * Purpose: Integration with Flash Express API
 * Authentication: API Key + Token
 * Services: Standard, Express, Same Day
 */
class FlashExpressApiService
{
    protected $baseUrl = 'https://open-api.flashexpress.com';
    
    /**
     * Get quote from Flash Express API
     * Credentials Required: api_key, api_token, shop_id
     */
    public function getQuote(array $credentials, array $packageData): array
    {
        try {
            // Validate required credentials
            if (!isset($credentials['api_key']) || !isset($credentials['api_token']) || !isset($credentials['shop_id'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required credentials: api_key, api_token, shop_id'
                ];
            }

            // Prepare request data
            $requestData = $this->prepareRequestData($packageData, $credentials);
            
            // Make API call
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $credentials['api_token'],
                    'X-API-Key' => $credentials['api_key']
                ])
                ->post($this->baseUrl . '/v1/rate/calculate', $requestData);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'success' => true,
                        'quote_price' => $data['data']['rate'] ?? 0,
                        'service_type' => $data['data']['service_name'] ?? 'Standard',
                        'estimated_days' => $data['data']['transit_days'] ?? 1,
                        'reference' => $data['data']['rate_id'] ?? null
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
            Log::error('Flash Express API Error', [
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
     * Prepare request data for Flash Express API
     */
    protected function prepareRequestData(array $packageData, array $credentials): array
    {
        return [
            'shop_id' => $credentials['shop_id'],
            'parcel' => [
                'weight' => $packageData['package']['weight'] ?? 1.0,
                'dimensions' => [
                    'length' => $packageData['package']['length'] ?? 10,
                    'width' => $packageData['package']['width'] ?? 10,
                    'height' => $packageData['package']['height'] ?? 10,
                ]
            ],
            'origin' => [
                'postcode' => $packageData['pickup']['postcode'] ?? '10110'
            ],
            'destination' => [
                'postcode' => $packageData['delivery']['postcode'] ?? '10120'
            ],
            'service_type' => $packageData['service_type'] ?? 'STANDARD'
        ];
    }

    /**
     * Test connection to Flash Express API
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
            Log::error('Flash Express connection test failed', [
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
            'SAME_DAY' => 'Same Day Delivery'
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

        if (!isset($credentials['api_token']) || empty($credentials['api_token'])) {
            $errors[] = 'API Token is required';
        }

        if (!isset($credentials['shop_id']) || empty($credentials['shop_id'])) {
            $errors[] = 'Shop ID is required';
        }

        return $errors;
    }
} 
<?php

namespace Modules\Shipper\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ThailandPostApiService
 * Purpose: Integration with Thailand Post API
 * Authentication: API Key
 * Services: EMS, Registered Mail, Surface Mail
 */
class ThailandPostApiService
{
    protected $baseUrl = 'https://trackapi.thailandpost.co.th';
    
    /**
     * Get quote from Thailand Post API
     * Credentials Required: api_key, username
     */
    public function getQuote(array $credentials, array $packageData): array
    {
        try {
            // Validate required credentials
            if (!isset($credentials['api_key']) || !isset($credentials['username'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required credentials: api_key, username'
                ];
            }

            // Prepare request data
            $requestData = $this->prepareRequestData($packageData, $credentials);
            
            // Make API call
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $credentials['api_key'],
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/v1/quote', $requestData);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return [
                        'success' => true,
                        'quote_price' => $data['price'] ?? 0,
                        'service_type' => $data['service_type'] ?? 'EMS',
                        'estimated_days' => $data['estimated_delivery_days'] ?? 3,
                        'reference' => $data['reference'] ?? null
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
            Log::error('Thailand Post API Error', [
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
     * Prepare request data for Thailand Post API
     */
    protected function prepareRequestData(array $packageData, array $credentials): array
    {
        return [
            'username' => $credentials['username'],
            'package' => [
                'weight' => $packageData['package']['weight'] ?? 1.0,
                'length' => $packageData['package']['length'] ?? 10,
                'width' => $packageData['package']['width'] ?? 10,
                'height' => $packageData['package']['height'] ?? 10
            ],
            'pickup' => [
                'postcode' => $packageData['pickup']['postcode'] ?? '10110'
            ],
            'delivery' => [
                'postcode' => $packageData['delivery']['postcode'] ?? '10120'
            ],
            'service_type' => $packageData['service_type'] ?? 'EMS'
        ];
    }

    /**
     * Test connection to Thailand Post API
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
            Log::error('Thailand Post connection test failed', [
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
            'EMS' => 'Express Mail Service',
            'REGISTERED' => 'Registered Mail',
            'SURFACE' => 'Surface Mail'
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

        if (!isset($credentials['username']) || empty($credentials['username'])) {
            $errors[] = 'Username is required';
        }

        return $errors;
    }
} 
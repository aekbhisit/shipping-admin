<?php

namespace Modules\Audit\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

/**
 * DataProtectionService
 * Purpose: Sensitive data protection in audit logs
 */
class DataProtectionService
{
    private array $sensitiveFields = [
        'password', 'password_hash', 'remember_token',
        'api_key', 'api_token', 'secret_key', 'access_token',
        'credit_card', 'card_number', 'cvv', 'cc_number',
        'ssn', 'social_security', 'tax_id', 'national_id',
        'bank_account', 'routing_number', 'iban'
    ];

    private array $hashingFields = [
        'password', 'password_hash', 'remember_token',
        'api_key', 'api_token', 'secret_key', 'access_token'
    ];

    private array $encryptionFields = [
        'credit_card', 'card_number', 'cvv', 'cc_number',
        'ssn', 'social_security', 'tax_id', 'national_id',
        'bank_account', 'routing_number', 'iban'
    ];

    /**
     * Hash sensitive data that should be one-way protected
     */
    public function hashSensitiveData(array $data): array
    {
        $protectedData = $data;

        foreach ($this->hashingFields as $field) {
            if (array_key_exists($field, $protectedData) && !empty($protectedData[$field])) {
                // Create a hash that allows for pattern recognition but not recovery
                $protectedData[$field] = '[HASHED:' . substr(hash('sha256', $protectedData[$field]), 0, 8) . ']';
            }
        }

        return $protectedData;
    }

    /**
     * Mask sensitive fields for display purposes
     */
    public function maskSensitiveFields(array $data): array
    {
        $maskedData = $data;

        foreach ($this->sensitiveFields as $field) {
            if (array_key_exists($field, $maskedData) && !empty($maskedData[$field])) {
                $value = $maskedData[$field];
                
                if (in_array($field, $this->hashingFields)) {
                    $maskedData[$field] = '[PROTECTED]';
                } elseif (in_array($field, $this->encryptionFields)) {
                    $maskedData[$field] = $this->maskValue($value);
                } else {
                    $maskedData[$field] = '[SENSITIVE]';
                }
            }
        }

        return $maskedData;
    }

    /**
     * Encrypt audit data for storage
     */
    public function encryptAuditData(array $data): array
    {
        $encryptedData = $data;

        foreach ($this->encryptionFields as $field) {
            if (array_key_exists($field, $encryptedData) && !empty($encryptedData[$field])) {
                try {
                    $encryptedData[$field] = '[ENCRYPTED:' . Crypt::encryptString($encryptedData[$field]) . ']';
                } catch (\Exception $e) {
                    // If encryption fails, mask the data instead
                    $encryptedData[$field] = '[ENCRYPTION_FAILED]';
                }
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt audit data for authorized viewing
     */
    public function decryptAuditData(array $encryptedData): array
    {
        $decryptedData = $encryptedData;

        foreach ($encryptedData as $field => $value) {
            if (is_string($value) && strpos($value, '[ENCRYPTED:') === 0) {
                try {
                    $encryptedValue = substr($value, 11, -1); // Remove [ENCRYPTED: and ]
                    $decryptedData[$field] = Crypt::decryptString($encryptedValue);
                } catch (\Exception $e) {
                    // If decryption fails, keep it masked
                    $decryptedData[$field] = '[DECRYPTION_FAILED]';
                }
            }
        }

        return $decryptedData;
    }

    /**
     * Comprehensive protection for audit logs
     */
    public function protectAuditData(array $data): array
    {
        // First apply hashing for authentication-related fields
        $protectedData = $this->hashSensitiveData($data);
        
        // Then apply encryption for financial/personal data
        $protectedData = $this->encryptAuditData($protectedData);
        
        return $protectedData;
    }

    /**
     * Check if a field contains sensitive data
     */
    public function isSensitiveField(string $fieldName): bool
    {
        return in_array(strtolower($fieldName), $this->sensitiveFields);
    }

    /**
     * Get list of protected fields for configuration
     */
    public function getSensitiveFields(): array
    {
        return $this->sensitiveFields;
    }

    /**
     * Add custom sensitive field
     */
    public function addSensitiveField(string $fieldName, string $protectionType = 'mask'): void
    {
        $fieldName = strtolower($fieldName);
        
        if (!in_array($fieldName, $this->sensitiveFields)) {
            $this->sensitiveFields[] = $fieldName;
        }

        switch ($protectionType) {
            case 'hash':
                if (!in_array($fieldName, $this->hashingFields)) {
                    $this->hashingFields[] = $fieldName;
                }
                break;
            case 'encrypt':
                if (!in_array($fieldName, $this->encryptionFields)) {
                    $this->encryptionFields[] = $fieldName;
                }
                break;
        }
    }

    /**
     * Remove sensitive field from protection
     */
    public function removeSensitiveField(string $fieldName): void
    {
        $fieldName = strtolower($fieldName);
        
        $this->sensitiveFields = array_filter($this->sensitiveFields, function($field) use ($fieldName) {
            return $field !== $fieldName;
        });
        
        $this->hashingFields = array_filter($this->hashingFields, function($field) use ($fieldName) {
            return $field !== $fieldName;
        });
        
        $this->encryptionFields = array_filter($this->encryptionFields, function($field) use ($fieldName) {
            return $field !== $fieldName;
        });
    }

    /**
     * Validate data protection configuration
     */
    public function validateProtectionConfig(): array
    {
        $issues = [];

        // Check for overlapping protection methods
        $hashAndEncrypt = array_intersect($this->hashingFields, $this->encryptionFields);
        if (!empty($hashAndEncrypt)) {
            $issues[] = [
                'type' => 'overlap',
                'message' => 'Fields configured for both hashing and encryption',
                'fields' => $hashAndEncrypt
            ];
        }

        // Check encryption key availability
        try {
            Crypt::encryptString('test');
        } catch (\Exception $e) {
            $issues[] = [
                'type' => 'encryption_key',
                'message' => 'Encryption key not properly configured',
                'error' => $e->getMessage()
            ];
        }

        return [
            'status' => empty($issues) ? 'valid' : 'issues_found',
            'issues' => $issues,
            'total_sensitive_fields' => count($this->sensitiveFields),
            'hashed_fields' => count($this->hashingFields),
            'encrypted_fields' => count($this->encryptionFields)
        ];
    }

    /**
     * Generate data protection report
     */
    public function generateProtectionReport(): array
    {
        return [
            'protection_summary' => [
                'total_sensitive_fields' => count($this->sensitiveFields),
                'hashed_fields' => count($this->hashingFields),
                'encrypted_fields' => count($this->encryptionFields),
                'masked_only_fields' => count($this->sensitiveFields) - count($this->hashingFields) - count($this->encryptionFields)
            ],
            'field_configuration' => [
                'sensitive_fields' => $this->sensitiveFields,
                'hashed_fields' => $this->hashingFields,
                'encrypted_fields' => $this->encryptionFields
            ],
            'validation' => $this->validateProtectionConfig(),
            'recommendations' => $this->getProtectionRecommendations()
        ];
    }

    /**
     * Clean sensitive data from log display
     */
    public function cleanForDisplay(array $auditData): array
    {
        $cleanData = [];

        foreach ($auditData as $key => $value) {
            if ($this->isSensitiveField($key)) {
                $cleanData[$key] = $this->formatSensitiveDisplay($key, $value);
            } else {
                $cleanData[$key] = $value;
            }
        }

        return $cleanData;
    }

    /**
     * Get protection statistics
     */
    public function getProtectionStatistics(): array
    {
        // This would typically analyze actual audit logs
        return [
            'total_protected_logs' => 0, // Would query database
            'logs_with_sensitive_data' => 0, // Would query database
            'protection_coverage' => 100, // Percentage of sensitive data properly protected
            'last_protection_check' => now()->format('Y-m-d H:i:s')
        ];
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Mask a value for display
     */
    private function maskValue(string $value): string
    {
        if (strlen($value) <= 4) {
            return str_repeat('*', strlen($value));
        }

        // Show first 2 and last 2 characters
        $start = substr($value, 0, 2);
        $end = substr($value, -2);
        $middle = str_repeat('*', strlen($value) - 4);

        return $start . $middle . $end;
    }

    /**
     * Format sensitive data for display
     */
    private function formatSensitiveDisplay(string $field, $value): string
    {
        if (is_string($value)) {
            if (strpos($value, '[HASHED:') === 0) {
                return '[PROTECTED - HASHED]';
            } elseif (strpos($value, '[ENCRYPTED:') === 0) {
                return '[PROTECTED - ENCRYPTED]';
            } elseif (in_array($field, $this->sensitiveFields)) {
                return '[SENSITIVE DATA]';
            }
        }

        return (string) $value;
    }

    /**
     * Get protection recommendations
     */
    private function getProtectionRecommendations(): array
    {
        $recommendations = [];

        // Check if all sensitive fields have protection
        $unprotectedFields = array_diff(
            $this->sensitiveFields, 
            array_merge($this->hashingFields, $this->encryptionFields)
        );

        if (!empty($unprotectedFields)) {
            $recommendations[] = [
                'type' => 'unprotected_fields',
                'priority' => 'medium',
                'message' => 'Some sensitive fields only use basic masking',
                'fields' => $unprotectedFields,
                'suggestion' => 'Consider adding hashing or encryption for these fields'
            ];
        }

        // Check encryption configuration
        $validation = $this->validateProtectionConfig();
        if ($validation['status'] !== 'valid') {
            $recommendations[] = [
                'type' => 'configuration',
                'priority' => 'high',
                'message' => 'Protection configuration has issues',
                'details' => $validation['issues']
            ];
        }

        // General security recommendations
        $recommendations[] = [
            'type' => 'security',
            'priority' => 'low',
            'message' => 'Regular security audit recommended',
            'suggestion' => 'Review and update sensitive field list quarterly'
        ];

        return $recommendations;
    }

    /**
     * Scan data for potential sensitive content
     */
    public function scanForSensitiveContent(array $data): array
    {
        $findings = [];

        foreach ($data as $field => $value) {
            if (!is_string($value)) {
                continue;
            }

            // Check for credit card patterns
            if (preg_match('/\b\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4}\b/', $value)) {
                $findings[] = [
                    'field' => $field,
                    'type' => 'credit_card',
                    'confidence' => 'high'
                ];
            }

            // Check for SSN patterns
            if (preg_match('/\b\d{3}[\s\-]?\d{2}[\s\-]?\d{4}\b/', $value)) {
                $findings[] = [
                    'field' => $field,
                    'type' => 'ssn',
                    'confidence' => 'medium'
                ];
            }

            // Check for email patterns in unexpected fields
            if (preg_match('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $value) && 
                !in_array($field, ['email', 'contact_email'])) {
                $findings[] = [
                    'field' => $field,
                    'type' => 'email',
                    'confidence' => 'low'
                ];
            }

            // Check for phone number patterns
            if (preg_match('/\b\d{3}[\s\-]?\d{3}[\s\-]?\d{4}\b/', $value) && 
                !in_array($field, ['phone', 'mobile', 'telephone'])) {
                $findings[] = [
                    'field' => $field,
                    'type' => 'phone',
                    'confidence' => 'low'
                ];
            }
        }

        return $findings;
    }

    /**
     * Auto-protect data based on content scanning
     */
    public function autoProtectData(array $data): array
    {
        $protectedData = $data;
        $findings = $this->scanForSensitiveContent($data);

        foreach ($findings as $finding) {
            if ($finding['confidence'] === 'high') {
                $field = $finding['field'];
                
                switch ($finding['type']) {
                    case 'credit_card':
                        $protectedData[$field] = $this->maskValue($protectedData[$field]);
                        break;
                    case 'ssn':
                        $protectedData[$field] = '[SSN_DETECTED]';
                        break;
                }
            }
        }

        return $protectedData;
    }
} 
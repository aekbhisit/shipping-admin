<?php

namespace Modules\Shipper\Repositories\Contracts;

interface ApiLogRepositoryInterface
{
    /**
     * Create API log entry
     */
    public function createApiLog(array $data);

    /**
     * Get logs by carrier with filters
     */
    public function getLogsByCarrier($carrierId, array $filters = []);

    /**
     * Get recent logs
     */
    public function getRecentLogs($limit = 100);

    /**
     * Get error logs
     */
    public function getErrorLogs();

    /**
     * Clean old logs
     */
    public function cleanOldLogs($daysToKeep = 30);

    /**
     * Get logs for DataTable
     */
    public function getForDataTable($request);

    /**
     * Get API statistics
     */
    public function getApiStatistics($carrierId = null, $hours = 24);

    /**
     * Get success rate by carrier
     */
    public function getSuccessRateByCarrier($carrierId, $hours = 24);
} 
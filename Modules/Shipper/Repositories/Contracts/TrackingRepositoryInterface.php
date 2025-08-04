<?php

namespace Modules\Shipper\Repositories\Contracts;

interface TrackingRepositoryInterface
{
    /**
     * Get tracking history for shipment
     */
    public function getTrackingHistory($shipmentId);

    /**
     * Get latest tracking update
     */
    public function getLatestTracking($shipmentId);

    /**
     * Create tracking update
     */
    public function createTrackingUpdate(array $data);

    /**
     * Update tracking
     */
    public function updateTracking($id, array $data);

    /**
     * Get tracking by tracking number
     */
    public function getTrackingByNumber($trackingNumber);

    /**
     * Get recent tracking updates
     */
    public function getRecentUpdates($days = 7);

    /**
     * Get tracking by status
     */
    public function getTrackingByStatus($status);
} 
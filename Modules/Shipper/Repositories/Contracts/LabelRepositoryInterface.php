<?php

namespace Modules\Shipper\Repositories\Contracts;

interface LabelRepositoryInterface
{
    /**
     * Find label by ID
     */
    public function find($id);

    /**
     * Get label by shipment and carrier
     */
    public function getLabelByShipment($shipmentId, $carrierId);

    /**
     * Create new label
     */
    public function createLabel(array $data);

    /**
     * Update label
     */
    public function updateLabel($id, array $data);

    /**
     * Delete label
     */
    public function deleteLabel($id);

    /**
     * Get all labels for shipment
     */
    public function getLabelsByShipment($shipmentId);

    /**
     * Get label by tracking number
     */
    public function getLabelByTrackingNumber($trackingNumber);

    /**
     * Check if label file exists
     */
    public function labelFileExists($id);
} 
<?php

namespace Modules\Shipper\Repositories\Contracts;

interface QuoteRepositoryInterface
{
    /**
     * Get quotes by shipment
     */
    public function getQuotesByShipment($shipmentId);

    /**
     * Create new quote
     */
    public function createQuote(array $data);

    /**
     * Update quote
     */
    public function updateQuote($id, array $data);

    /**
     * Select quote (mark as selected)
     */
    public function selectQuote($quoteId);

    /**
     * Get selected quote for shipment
     */
    public function getSelectedQuote($shipmentId);

    /**
     * Delete quotes by shipment
     */
    public function deleteQuotesByShipment($shipmentId);

    /**
     * Get quotes by carrier priority
     */
    public function getQuotesByCarrierPriority($shipmentId);

    /**
     * Check if quotes are expired
     */
    public function getActiveQuotes($shipmentId);

    /**
     * Bulk create quotes
     */
    public function bulkCreateQuotes(array $quotes);
} 
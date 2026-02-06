<?php

namespace App\Traits;

use App\Models\Invoice\Invoice;
use App\Models\PurchaseOrder\PurchaseOrder;
use Illuminate\Support\Facades\DB;

trait InvoiceManupulations
{
    /**
     * Calculate the total of an invoice.
     *
     * @return void
     */
    public function calcInvoiceTotal(Invoice $invoice)
    {
        $total = $invoice->details()->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));
        $totalPurchases = $invoice->details()->sum(DB::raw('quantity * COALESCE(purchases_price, 0)'));
        $invoice->update([
            'total' => $total,
            'total_purchases' => $totalPurchases,
        ]);
    }

    /**
     * Calculate the total purchases of an invoice.
     *
     * @return void
     */
    public function calcInvoiceTotalPurchases(PurchaseOrder $purchaseOrder)
    {
        $total = $purchaseOrder->details()->sum(DB::raw('quantity * COALESCE(unit_price, 0)'));
        $purchaseOrder->update([
            'total' => $total,
        ]);
    }

    /**
     * Validate invoice parties relationship.
     * For invoices: supplier must be my_company, client must not be my_company.
     *
     * @param  Collection  $parties
     */
    private function validateInvoiceTypeParties(int $clientId, int $supplierId, $parties): array
    {
        // Check that client and supplier are not the same
        if ($clientId === $supplierId) {
            return ['success' => false, 'message' => __('invoice.validation.client_and_supplier_must_be_different')];
        }

        $client = $parties->where('id', $clientId)->first();
        $supplier = $parties->where('id', $supplierId)->first();

        // For invoices: supplier must be my_company, client must not be my_company
        if (! $supplier->is_my_company) {
            return ['success' => false, 'message' => __('invoice.validation.supplier_must_be_my_company')];
        }
        if ($client->is_my_company) {
            return ['success' => false, 'message' => __('invoice.validation.client_must_not_be_my_company')];
        }

        return ['success' => true, 'message' => null];
    }

    /**
     * Validate purchase order parties relationship.
     *  For purchase orders:
     * - Supplier should NOT be "my company" (external supplier)
     * - Client should be "my company" (buying for my company)
     * - Client and supplier must be different
     *
     * @param  Collection  $parties
     */
    private function validatePurchaseOrderParties(int $clientId, int $supplierId, $parties): array
    {
        // Check that client and supplier are not the same
        if ($clientId === $supplierId) {
            return ['success' => false, 'message' => __('purchase-order.validation.client_and_supplier_must_be_different')];
        }

        $client = $parties->where('id', $clientId)->first();
        $supplier = $parties->where('id', $supplierId)->first();

        // Client must be "my company"
        if (! $client->is_my_company) {
            return ['success' => false, 'message' => __('purchase-order.validation.client_must_be_my_company')];
        }

        // Supplier must NOT be "my company"
        if ($supplier->is_my_company) {
            return ['success' => false, 'message' => __('purchase-order.validation.supplier_must_not_be_my_company')];
        }

        return ['success' => true, 'message' => null];
    }
}

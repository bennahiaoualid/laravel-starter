<?php

namespace Database\Seeders;

use App\InvoiceStatus;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceCost;
use App\Models\Invoice\InvoiceDetail;
use App\Models\Invoice\InvoiceInvestor;
use App\Models\Person\Investor;
use App\Models\Person\Partie;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parties = Partie::query()->inRandomOrder()->take(5)->get();
        $products = Product::query()->inRandomOrder()->get();

        if ($parties->isEmpty() || $products->isEmpty()) {
            return; // prerequisites not seeded
        }

        $currentYear = now()->format('Y');
        $invoiceNumber = 1;

        $banks = \App\Models\Bank::all();

        // Get parties separated by is_my_company
        $myCompanies = Partie::where('is_my_company', true)->get();
        $notMyCompanies = Partie::where('is_my_company', false)->get();

        foreach (range(1, 8) as $i) {
            // For invoices: supplier must be my_company=true, client must be my_company=false
            if ($myCompanies->isEmpty() || $notMyCompanies->isEmpty()) {
                // Fallback: if we don't have both types, skip this invoice
                continue;
            }
            $supplier = $myCompanies->random();
            $client = $notMyCompanies->random();

            // Random payment type with weights: cash (40%), bank (30%), check (15%), on_term (15%)
            $paymentTypeWeights = [
                'cash' => 40,
                'bank' => 30,
                'check' => 15,
                'on_term' => 15,
            ];
            $paymentType = $this->weightedRandom($paymentTypeWeights);

            // Randomly assign status: 50% completed, 50% pending
            $status = rand(0, 1) === 0
                ? InvoiceStatus::COMPLETED->value
                : InvoiceStatus::PENDING->value;

            // Prepare invoice data
            $invoiceData = [
                'client_id' => $client->id,
                'supplier_id' => $supplier->id,
                'paid' => rand(0, 1) ? rand(0, 2000) / 10 : 0,
                'payment_type' => $paymentType,
                'tva' => rand(1, 100),
                'total' => 0,
                'invoice_num' => str_pad($invoiceNumber, 3, '0', STR_PAD_LEFT).'/'.$currentYear,
                'invoice_date' => Carbon::now()->subDays(rand(0, 30)),
                'status' => $status,
            ];

            // Set bank fields based on payment type
            if ($paymentType === 'cash') {
                // All bank fields null for cash
                $invoiceData['supplier_bank_id'] = null;
                $invoiceData['supplier_account'] = null;
                $invoiceData['client_bank_id'] = null;
                $invoiceData['client_account'] = null;
                $invoiceData['client_check_number'] = null;
            } elseif ($paymentType === 'bank') {
                // Set supplier and client bank/account from parties if available, otherwise use random bank
                if ($supplier->bank_id && $supplier->bank_account) {
                    $invoiceData['supplier_bank_id'] = $supplier->bank_id;
                    $invoiceData['supplier_account'] = $supplier->bank_account;
                } elseif (! $banks->isEmpty()) {
                    $invoiceData['supplier_bank_id'] = $banks->random()->id;
                    $invoiceData['supplier_account'] = str_repeat('0', 7).rand(100000000, 999999999);
                }

                if ($client->bank_id && $client->bank_account) {
                    $invoiceData['client_bank_id'] = $client->bank_id;
                    $invoiceData['client_account'] = $client->bank_account;
                } elseif (! $banks->isEmpty()) {
                    $invoiceData['client_bank_id'] = $banks->random()->id;
                    $invoiceData['client_account'] = str_repeat('0', 7).rand(100000000, 999999999);
                }
                $invoiceData['client_check_number'] = null;
            } elseif (in_array($paymentType, ['check', 'on_term'])) {
                // Hide supplier bank/account, set client bank and check number
                $invoiceData['supplier_bank_id'] = null;
                $invoiceData['supplier_account'] = null;

                if ($client->bank_id) {
                    $invoiceData['client_bank_id'] = $client->bank_id;
                } elseif (! $banks->isEmpty()) {
                    $invoiceData['client_bank_id'] = $banks->random()->id;
                }

                $invoiceData['client_account'] = null;
                $invoiceData['client_check_number'] = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            }

            $invoice = Invoice::create($invoiceData);

            $detailsCount = rand(2, 10);
            if ($i == 1) {
                $detailsCount = 50;
                $this->command->info('Invoice 1 has 50 details');

            }
            $total = 0;
            $totalPurchases = 0;
            $usedProducts = $products->random(min($detailsCount, $products->count()));
            if ($i == 1) {
                $this->command->info('count of used products: '.$usedProducts->count());

            }
            foreach ($usedProducts as $product) {
                $quantity = rand(1, 20);

                // For invoices: unit_price greater than purchases_price
                $purchasesPrice = rand(50, 4500) / 10; // 5.0 - 450.0
                $unitPrice = $purchasesPrice + rand(100, 1000) / 10; // purchases_price + (10.0 - 100.0)

                $total += $quantity * $unitPrice;
                $totalPurchases += $quantity * $purchasesPrice;

                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'purchases_price' => $purchasesPrice,
                ]);
            }

            $invoice->update([
                'total' => round($total, 2),
                'total_purchases' => round($totalPurchases, 2),
            ]);

            // Add costs (costs should be less than total)
            if ($total > 0) {
                $costDescriptions = [
                    'Transportation Cost',
                    'Packaging Cost',
                    'Handling Fee',
                    'Delivery Charge',
                    'Insurance Fee',
                    'Storage Cost',
                    'Administrative Fee',
                ];

                // Create 1-3 costs per invoice
                $costsCount = rand(1, 3);
                $totalCosts = 0;
                $maxTotalCosts = $total * 0.3; // Costs should not exceed 30% of invoice total

                for ($j = 0; $j < $costsCount; $j++) {
                    // Calculate remaining available cost amount
                    $remainingCostBudget = $maxTotalCosts - $totalCosts;

                    if ($remainingCostBudget <= 0) {
                        break; // Stop if we've reached the limit
                    }

                    // Each cost should be between 5% and 15% of invoice total, but not exceed remaining budget
                    $maxCostValue = min($total * 0.15, $remainingCostBudget);
                    $minCostValue = min($total * 0.05, $maxCostValue);

                    if ($minCostValue >= $maxCostValue) {
                        $costValue = $minCostValue;
                    } else {
                        $costValue = rand((int) ($minCostValue * 100), (int) ($maxCostValue * 100)) / 100;
                    }

                    InvoiceCost::create([
                        'invoice_id' => $invoice->id,
                        'description' => $costDescriptions[array_rand($costDescriptions)],
                        'value' => round($costValue, 2),
                    ]);

                    $totalCosts += $costValue;
                }
            }

            // Add investors to invoice (1-2 investors per invoice, less than 3)
            $investors = Investor::inRandomOrder()->take(rand(1, 2))->get();
            if ($investors->isNotEmpty()) {

                foreach ($investors as $investor) {
                    // Random percentage between 10% and 50%
                    $percentage = rand(1000, 5000) / 100; // 10.00 - 50.00
                    $amount = rand(1000, 5000) / 100; // 10.00 - 50.00

                    InvoiceInvestor::create([
                        'invoice_id' => $invoice->id,
                        'investor_id' => $investor->id,
                        'percentage' => $percentage,
                        'amount' => round($amount, 2),
                    ]);
                }
            }

            $invoiceNumber++;
        }
    }

    /**
     * Weighted random selection helper
     */
    private function weightedRandom(array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $key => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $key;
            }
        }

        return array_key_first($weights); // Fallback
    }
}

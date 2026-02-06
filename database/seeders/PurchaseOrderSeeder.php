<?php

namespace Database\Seeders;

use App\Models\Person\Partie;
use App\Models\Product\Product;
use App\Models\PurchaseOrder\PurchaseOrder;
use App\Models\PurchaseOrder\PurchaseOrderDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PurchaseOrderSeeder extends Seeder
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
        $orderNumber = 1;

        // Get parties separated by is_my_company
        $myCompanies = Partie::where('is_my_company', true)->get();
        $notMyCompanies = Partie::where('is_my_company', false)->get();

        if ($myCompanies->isEmpty() || $notMyCompanies->isEmpty()) {
            $this->command->warn('PurchaseOrderSeeder: Need both my_company and non-my_company parties to create purchase orders.');

            return;
        }

        // Create 10 purchase orders
        foreach (range(1, 10) as $i) {
            // Randomly select client and supplier
            // For purchase orders, typically: client is my_company, supplier is not my_company
            $client = $myCompanies->random();
            $supplier = $notMyCompanies->random();

            // Prepare purchase order data
            $purchaseOrderData = [
                'client_id' => $client->id,
                'supplier_id' => $supplier->id,
                'tva' => rand(1, 100),
                'order_num' => str_pad($orderNumber, 3, '0', STR_PAD_LEFT).'/'.$currentYear,
                'order_date' => Carbon::now()->subDays(rand(0, 60)),
            ];

            $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

            // Create purchase order details (2-8 products per order)
            $detailsCount = rand(2, 8);
            $usedProducts = $products->random(min($detailsCount, $products->count()));
            $total = 0;
            foreach ($usedProducts as $product) {
                $quantity = rand(1, 50);
                $unitPrice = rand(100, 5000) / 10; // 10.0 - 500.0

                $total += $quantity * $unitPrice;

                PurchaseOrderDetail::create([
                    'purchases_order_id' => $purchaseOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            $purchaseOrder->update([
                'total' => $total,
            ]);

            $orderNumber++;
        }

        $this->command->info('Created 10 purchase orders with details.');
    }
}

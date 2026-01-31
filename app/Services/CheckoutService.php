<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class CheckoutService
{
    public function processCheckout(array $data)
    {
        return DB::transaction(function () use ($data) {
            $total = 0;
            $items = json_decode($data['cart_data'], true);

            // 1. Validate Cart & Stock (Fail Fast)
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->find($item['id']); // Lock row to prevent race conditions
                
                if (!$product) {
                    throw new Exception("Product ID {$item['id']} not found.");
                }
                if ($product->qty < $item['qty']) {
                    throw new Exception("Insufficient stock for {$product->name}. Requested: {$item['qty']}, Available: {$product->qty}");
                }
            }

            // 2. Create Sale Header
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_id' => $data['customer_id'] ?? null,
                'invoice_number' => 'INV-' . strtoupper(uniqid()) . '-' . date('Ymd'),
                'payment_type' => $data['payment_type'],
                'total_amount' => 0, // Calculated below
                'discount' => 0,     // Implement promo logic here if needed
                'tax' => 0,
                'final_total' => 0,
            ]);

            // 3. Process Items
            foreach ($items as $item) {
                $product = Product::find($item['id']);
                
                // Check for Active Promotions
                $discount = 0;
                $activePromo = $product->promotions()
                    ->where('is_active', true)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->first();

                if ($activePromo) {
                    if ($activePromo->type == 'percent') {
                        $discount = ($product->sale_price * ($activePromo->discount_value / 100));
                    } else {
                        $discount = $activePromo->discount_value;
                    }
                }

                $finalPrice = max(0, $product->sale_price - $discount);
                $subtotal = $finalPrice * $item['qty'];
                $total += $subtotal;

                // Create Snapshot (Sale Detail)
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->sale_price, // Original Price
                    'discount' => $discount,         // Discount Applied
                    'subtotal' => $subtotal
                ]);

                // 4. Atomic Decrement (MySQL)
                $product->decrement('qty', $item['qty']);

                // 5. Log Stock Transaction
                StockTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'sale',
                    'qty' => -$item['qty'], // Negative for sale
                    'date' => now(),
                ]);
            }

            // Update Totals
            $sale->update([
                'total_amount' => $total,
                'final_total' => $total // Add Tax logic here if needed
            ]);

            return $sale;
        });
    }
}
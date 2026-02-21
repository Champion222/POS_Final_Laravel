<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::select('id', 'name', 'sale_price', 'image', 'qty', 'barcode', 'category_id')->where('qty', '>', 0)->get();
        $categories = Category::select('id', 'name')->get();
        $customers = Customer::select('id', 'name')->get();
        $barcodeIndex = $products
            ->filter(fn (Product $product) => filled($product->barcode))
            ->mapWithKeys(fn (Product $product) => [
                strtolower((string) $product->barcode) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->sale_price,
                    'image' => $product->image,
                ],
            ])
            ->all();

        $promotionIndex = $this->promotionIndex($products);

        return view('pos.index', compact('products', 'categories', 'customers', 'barcodeIndex', 'promotionIndex'));
    }

    public function stock(): JsonResponse
    {
        $stocks = Product::query()
            ->select('id', 'qty')
            ->pluck('qty', 'id');

        return response()->json([
            'status' => 'success',
            'stocks' => $stocks,
        ]);
    }

    public function promotions(): JsonResponse
    {
        $products = Product::query()
            ->select('id', 'sale_price')
            ->get();

        return response()->json([
            'status' => 'success',
            'promotions' => $this->promotionIndex($products),
        ]);
    }

    public function generateKhqr(Request $request): JsonResponse
    {
        $amount = round((float) $request->amount, 2);
        if ($amount <= 0) {
            return response()->json(['status' => 'error']);
        }

        $info = IndividualInfo::withOptionalArray(
            config('services.bakong.merchant_id', 'khqr@devb'),
            config('services.bakong.merchant_name', 'GenZPOS Store'),
            config('services.bakong.city', 'Phnom Penh'),
            [
                'currency' => KHQRData::CURRENCY_USD,
                'amount' => $amount,
            ]
        );

        $khqr = BakongKHQR::generateIndividual($info);
        $qrImage = QrCode::size(300)->format('svg')->margin(0)->generate($khqr->data['qr']);

        return response()->json([
            'status' => 'success',
            'qr_svg' => trim($qrImage),
            'qr_string' => $khqr->data['qr'],
            'md5' => $khqr->data['md5'],
            'amount' => number_format($amount, 2),
        ]);
    }

    public function checkKhqrStatus(Request $request): JsonResponse
    {
        try {
            $token = config('services.bakong.token');

            if ($token) {
                // Real Bakong Check
                $bakong = new BakongKHQR($token);
                $result = $bakong->checkTransactionByMD5($request->md5);

                return response()->json($result);
            } else {
                // Simulation (If you don't have a token yet)
                $isPaid = rand(0, 10) > 8;

                return response()->json(['responseCode' => $isPaid ? 0 : 1]);
            }
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 1]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $cart = $request->input('cart_data', []);
        $items = collect($cart)
            ->filter(fn ($item) => is_array($item) && isset($item['id'], $item['qty']))
            ->map(fn ($item) => [
                'id' => (int) $item['id'],
                'qty' => (int) $item['qty'],
            ])
            ->filter(fn (array $item) => $item['id'] > 0 && $item['qty'] > 0)
            ->values();

        if ($items->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty.',
            ], 422);
        }

        try {
            [$sale, $receiptItems] = DB::transaction(function () use ($items, $request): array {
                $productIds = $items->pluck('id')->unique()->values();
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $product = $products->get($item['id']);
                    if (! $product) {
                        throw new \Exception("Product ID {$item['id']} not found.");
                    }

                    if ($product->qty < $item['qty']) {
                        throw new \Exception("Insufficient stock for {$product->name}.");
                    }
                }

                $promotionIndex = $this->promotionIndex($products->values());

                $sale = Sale::create([
                    'user_id' => Auth::id(),
                    'customer_id' => $request->input('customer_id'),
                    'invoice_number' => 'INV-'.strtoupper(uniqid()),
                    'payment_type' => (string) $request->input('payment_type', 'cash'),
                    'total_amount' => 0,
                    'discount' => 0,
                    'tax' => 0,
                    'final_total' => 0,
                ]);

                $subtotal = 0.0;
                $discountTotal = 0.0;
                $receiptItems = [];

                foreach ($items as $item) {
                    $product = $products->get($item['id']);
                    $qty = $item['qty'];
                    $unitPrice = (float) $product->sale_price;
                    $unitDiscount = (float) ($promotionIndex[$product->id]['discount_amount'] ?? 0);
                    $unitDiscount = min($unitDiscount, $unitPrice);
                    $lineSubtotal = round(($unitPrice - $unitDiscount) * $qty, 2);

                    $subtotal += $unitPrice * $qty;
                    $discountTotal += $unitDiscount * $qty;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $unitPrice,
                        'discount' => $unitDiscount,
                        'subtotal' => $lineSubtotal,
                    ]);

                    $product->decrement('qty', $qty);

                    $receiptItems[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => max(0, $unitPrice - $unitDiscount),
                        'qty' => $qty,
                        'image' => $product->image,
                    ];
                }

                $finalTotal = max(0, $subtotal - $discountTotal);
                $sale->update([
                    'total_amount' => round($subtotal, 2),
                    'discount' => round($discountTotal, 2),
                    'final_total' => round($finalTotal, 2),
                ]);

                return [$sale, $receiptItems];
            });

            $this->sendTelegramReceiptNotification($sale, $receiptItems);

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return array<int, array{id:int, name:string, type:string, value:float, label:string, discount_amount:float, end_date:string|null}>
     */
    private function promotionIndex(Collection $products): array
    {
        $productsById = $products->keyBy('id');
        if ($productsById->isEmpty()) {
            return [];
        }

        $promotions = Promotion::query()
            ->active()
            ->whereHas('products', fn ($query) => $query->whereIn('products.id', $productsById->keys()))
            ->with(['products' => fn ($query) => $query->select('products.id')->whereIn('products.id', $productsById->keys())])
            ->get(['id', 'name', 'discount_value', 'type', 'start_date', 'end_date', 'is_active']);

        $index = [];
        foreach ($promotions as $promotion) {
            foreach ($promotion->products as $product) {
                $basePrice = (float) ($productsById[$product->id]->sale_price ?? 0);
                $discountAmount = $this->promotionDiscount($promotion, $basePrice);
                if ($discountAmount <= 0) {
                    continue;
                }

                $current = $index[$product->id] ?? null;
                if ($current && $discountAmount <= $current['discount_amount']) {
                    continue;
                }

                $index[$product->id] = [
                    'id' => $promotion->id,
                    'name' => $promotion->name,
                    'type' => $promotion->type,
                    'value' => (float) $promotion->discount_value,
                    'label' => $this->promotionLabel($promotion),
                    'discount_amount' => round($discountAmount, 2),
                    'end_date' => $promotion->end_date?->toDateString(),
                ];
            }
        }

        return $index;
    }

    private function promotionDiscount(Promotion $promotion, float $price): float
    {
        if ($price <= 0) {
            return 0.0;
        }

        $discount = $promotion->type === 'percent'
            ? $price * ((float) $promotion->discount_value / 100)
            : (float) $promotion->discount_value;

        return min(max($discount, 0), $price);
    }

    private function promotionLabel(Promotion $promotion): string
    {
        $value = (float) $promotion->discount_value;
        if ($promotion->type === 'percent') {
            $formatted = rtrim(rtrim(number_format($value, 2), '0'), '.');

            return $formatted.'%';
        }

        return '$'.number_format($value, 2);
    }

    /**
     * @param  array<int, array{id:int, name:string, price:numeric, qty:int, image?:string|null}>  $cart
     */
    private function sendTelegramReceiptNotification(Sale $sale, array $cart): void
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return;
        }

        $cashierName = Auth::user()?->name ?? 'Unknown';
        $itemsCount = array_sum(array_map(static function (array $item): int {
            return (int) $item['qty'];
        }, $cart));

        $caption = $this->formatTelegramReceiptCaption($sale, $cart, $cashierName, $itemsCount);

        $photo = $this->resolveTelegramReceiptPhoto($cart);

        try {
            Http::attach('photo', $photo['contents'], $photo['filename'])->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * @param  array<int, array{id:int, name:string, price:numeric, qty:int, image?:string|null}>  $cart
     */
    private function formatTelegramReceiptCaption(Sale $sale, array $cart, string $cashierName, int $itemsCount): string
    {
        $escape = static fn (string $value): string => e($value);
        $subtotal = $sale->total_amount ?? $sale->final_total;
        $discount = $sale->discount ?? 0;
        $tax = $sale->tax ?? 0;
        $paymentLabel = $sale->payment_type === 'qr' ? 'KHQR' : strtoupper($sale->payment_type);

        $itemLines = array_map(static function (array $item) use ($escape): string {
            $name = $escape(str_replace(["\r", "\n"], ' ', (string) ($item['name'] ?? 'Item')));
            $qty = (int) ($item['qty'] ?? 0);
            $price = number_format((float) ($item['price'] ?? 0), 2);
            $lineSubtotal = number_format(((float) ($item['price'] ?? 0)) * $qty, 2);

            return "â€¢ {$name} x{$qty} @ \${$price} = \${$lineSubtotal}";
        }, $cart);

        $maxItems = 6;
        $visibleItemLines = array_slice($itemLines, 0, $maxItems);
        if (count($itemLines) > $maxItems) {
            $visibleItemLines[] = 'â€¢ +'.(count($itemLines) - $maxItems).' more items';
        }

        $discountPrefix = $discount > 0 ? '-' : '';

        $lines = array_merge([
            '<b>ðŸ§¾ GenZPOS Receipt</b>',
            '<b>Invoice:</b> <code>'.$escape($sale->invoice_number).'</code>',
            '<b>Date:</b> '.$escape($sale->created_at->format('Y-m-d H:i')),
            '<b>Cashier:</b> '.$escape($cashierName),
            '<b>Items:</b> '.$itemsCount,
            '',
            '<b>Items</b>',
        ], $visibleItemLines, [
            '',
            '<b>Totals</b>',
            'Subtotal: $'.number_format((float) $subtotal, 2),
            'Discount: '.$discountPrefix.'$'.number_format((float) $discount, 2),
            'Tax: $'.number_format((float) $tax, 2),
            '<b>Total:</b> $'.number_format((float) $sale->final_total, 2),
            '<b>Paid via:</b> '.$escape($paymentLabel),
        ]);

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, array{id:int, name:string, price:numeric, qty:int, image?:string|null}>  $cart
     * @return array{filename:string, contents:string}
     */
    private function resolveTelegramReceiptPhoto(array $cart): array
    {
        foreach ($cart as $item) {
            $image = $item['image'] ?? null;
            if (! $image) {
                continue;
            }

            $candidate = storage_path('app/public/'.ltrim((string) $image, '/'));
            if (! is_file($candidate)) {
                continue;
            }

            $contents = file_get_contents($candidate);
            if ($contents === false || $contents === '') {
                continue;
            }

            return [
                'filename' => basename($candidate),
                'contents' => $contents,
            ];
        }

        $fallbackPath = public_path('favicon.ico');
        if (is_file($fallbackPath)) {
            $contents = file_get_contents($fallbackPath);
            if ($contents !== false && $contents !== '') {
                return [
                    'filename' => 'receipt.ico',
                    'contents' => $contents,
                ];
            }
        }

        $fallbackBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIW2NgYGD4DwABBAEAe8lY8QAAAABJRU5ErkJggg==';
        $fallbackContents = base64_decode($fallbackBase64, true);

        return [
            'filename' => 'receipt.png',
            'contents' => $fallbackContents === false ? '' : $fallbackContents,
        ];
    }
}

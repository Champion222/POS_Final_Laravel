<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Category;
use App\Models\Customer;
use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Helpers\KHQRData;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::select('id', 'name', 'sale_price', 'image', 'qty', 'barcode', 'category_id')->where('qty', '>', 0)->get();
        $categories = Category::select('id', 'name')->get();
        $customers = Customer::select('id', 'name')->get();
        return view('pos.index', compact('products', 'categories', 'customers'));
    }

    public function generateKhqr(Request $request): JsonResponse
    {
        $amount = round((float) $request->amount, 2);
        if ($amount <= 0) {
            return response()->json(['status' => 'error']);
        }

        $info = new IndividualInfo(
            config('services.bakong.merchant_id', 'khqr@devb'),
            config('services.bakong.merchant_name', 'NexPOS Store'),
            config('services.bakong.city', 'Phnom Penh'),
            KHQRData::CURRENCY_USD,
            $amount
        );

        $khqr = BakongKHQR::generateIndividual($info);
        $qrImage = QrCode::size(300)->format('svg')->margin(0)->generate($khqr->data['qr']);

        return response()->json([
            'status' => 'success',
            'qr_svg' => trim($qrImage),
            'md5' => $khqr->data['md5'],
            'amount' => number_format($amount, 2)
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
        $cart = $request->cart_data; 
        
        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['qty'];
            }

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_id' => $request->customer_id,
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'payment_type' => $request->payment_type,
                'total_amount' => $total,
                'final_total' => $total,
            ]);

            foreach ($cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty']
                ]);
                Product::where('id', $item['id'])->decrement('qty', $item['qty']);
            }

            DB::commit();
            $this->sendTelegramReceiptNotification($sale, $cart);
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param array<int, array{id:int, name:string, price:numeric, qty:int, image?:string|null}> $cart
     */
    private function sendTelegramReceiptNotification(Sale $sale, array $cart): void
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
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
     * @param array<int, array{id:int, name:string, price:numeric, qty:int, image?:string|null}> $cart
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

            return "• {$name} x{$qty} @ \${$price} = \${$lineSubtotal}";
        }, $cart);

        $maxItems = 6;
        $visibleItemLines = array_slice($itemLines, 0, $maxItems);
        if (count($itemLines) > $maxItems) {
            $visibleItemLines[] = '• +' . (count($itemLines) - $maxItems) . ' more items';
        }

        $discountPrefix = $discount > 0 ? '-' : '';

        $lines = array_merge([
            '<b>🧾 NexPOS Receipt</b>',
            '<b>Invoice:</b> <code>' . $escape($sale->invoice_number) . '</code>',
            '<b>Date:</b> ' . $escape($sale->created_at->format('Y-m-d H:i')),
            '<b>Cashier:</b> ' . $escape($cashierName),
            '<b>Items:</b> ' . $itemsCount,
            '',
            '<b>Items</b>',
        ], $visibleItemLines, [
            '',
            '<b>Totals</b>',
            'Subtotal: $' . number_format((float) $subtotal, 2),
            'Discount: ' . $discountPrefix . '$' . number_format((float) $discount, 2),
            'Tax: $' . number_format((float) $tax, 2),
            '<b>Total:</b> $' . number_format((float) $sale->final_total, 2),
            '<b>Paid via:</b> ' . $escape($paymentLabel),
        ]);

        return implode("\n", $lines);
    }

    /**
     * @param array<int, array{id:int, name:string, price:numeric, qty:int, image?:string|null}> $cart
     * @return array{filename:string, contents:string}
     */
    private function resolveTelegramReceiptPhoto(array $cart): array
    {
        foreach ($cart as $item) {
            $image = $item['image'] ?? null;
            if (!$image) {
                continue;
            }

            $candidate = storage_path('app/public/' . ltrim((string) $image, '/'));
            if (!is_file($candidate)) {
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

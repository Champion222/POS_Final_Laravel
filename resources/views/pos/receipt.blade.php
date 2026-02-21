<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->invoice_number }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;600;800&display=swap');

        /* --- SCREEN STYLES (UI/UX) --- */
        body {
            background-color: #0f172a; /* Dark background for screen */
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 40px 0;
        }

        .receipt-container {
            background: white;
            width: 320px; /* Standard Thermal Width */
            padding: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); /* Deep shadow */
            position: relative;
            /* Jagged Edge Effect (Bottom) */
            /* clip-path: polygon(0 0, 100% 0, 100% 100%, 95% 98%, 90% 100%, 85% 98%, 80% 100%, 75% 98%, 70% 100%, 65% 98%, 60% 100%, 55% 98%, 50% 100%, 45% 98%, 40% 100%, 35% 98%, 30% 100%, 25% 98%, 20% 100%, 15% 98%, 10% 100%, 5% 98%, 0 100%); */
        }

        .actions {
            position: fixed;
            bottom: 30px;
            display: flex;
            gap: 15px;
            z-index: 100;
        }

        .btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            backdrop-filter: blur(10px);
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background: white;
            color: #0f172a;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: #4f46e5;
            border-color: #4f46e5;
        }
        .btn-primary:hover {
            background: #4338ca;
            color: white;
        }

        /* --- RECEIPT CONTENT --- */
        .brand {
            text-align: center;
            margin-bottom: 20px;
        }
        .brand h1 {
            font-size: 24px;
            font-weight: 900;
            margin: 0;
            letter-spacing: -1px;
            text-transform: uppercase;
        }
        .brand-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            margin: 0 auto 8px;
        }
        .brand p {
            font-size: 10px;
            color: #64748b;
            margin: 4px 0 0 0;
            font-family: 'Space Mono', monospace;
        }

        .divider {
            border-top: 2px dashed #e2e8f0;
            margin: 15px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-family: 'Space Mono', monospace;
            color: #475569;
            margin-bottom: 4px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .item-row {
            font-size: 12px;
            font-family: 'Space Mono', monospace;
            vertical-align: top;
        }
        
        .item-name {
            display: block;
            font-weight: 700;
            margin-bottom: 2px;
            color: #1e293b;
        }
        
        .item-calc {
            display: flex;
            justify-content: space-between;
            color: #64748b;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .total-section {
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .total-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
        }

        .total-amount {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
        }
        .footer p {
            font-size: 10px;
            color: #94a3b8;
            margin: 3px 0;
        }
        .barcode {
            margin: 15px auto;
            height: 40px;
            background: repeating-linear-gradient(
                90deg,
                #000 0,
                #000 2px,
                #fff 2px,
                #fff 4px
            );
            width: 80%;
            opacity: 0.8;
        }

        /* --- PRINT MEDIA QUERY --- */
        @media print {
            body {
                background: none;
                padding: 0;
                display: block;
            }
            .receipt-container {
                box-shadow: none;
                width: 100%; /* Full width of paper */
                padding: 0;
            }
            .actions {
                display: none;
            }
            .total-section {
                background: none !important; /* Save Ink */
                border-top: 2px solid #000;
                border-bottom: 2px solid #000;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        
        <div class="brand">
            <img class="brand-logo" src="https://i.postimg.cc/fTtdBdZf/Chat-GPT-Image-Feb-7-2026-03-27-39-PM.png" alt="GenZPOS logo">
            <h1>GenZPOS</h1>
            <p>PHNOM PENH, CAMBODIA</p>
            <p>TEL: +855 12 345 678</p>
        </div>

        <div class="divider"></div>

        <div class="info-row">
            <span>INV: #{{ $sale->invoice_number }}</span>
            <span>{{ $sale->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>CASHIER: {{ strtoupper($sale->cashier?->name ?? 'Unknown') }}</span>
        </div>

        <div class="divider"></div>

        <div class="items-list">
            @foreach($sale->details as $item)
            @php
                $unitDiscount = $item->discount ?? 0;
                $unitPrice = max(0, $item->price - $unitDiscount);
            @endphp
            <div class="item-row">
                <span class="item-name">{{ $item->product?->name ?? 'Unknown Item' }}</span>
                <div class="item-calc">
                    <span>
                        {{ $item->qty }} x ${{ number_format($unitPrice, 2) }}
                        @if($unitDiscount > 0)
                            <span style="font-size: 10px; color: #10b981; font-weight: 700;">(Promo)</span>
                        @endif
                    </span>
                    <span style="color: #0f172a; font-weight: bold;">${{ number_format($item->subtotal, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="total-section">
            @php
                $subtotal = $sale->total_amount ?? $sale->final_total;
                $discount = $sale->discount ?? 0;
                $tax = $sale->tax ?? 0;
            @endphp
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span style="font-family: 'Space Mono'; font-size: 12px;">${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Discount</span>
                <span style="font-family: 'Space Mono'; font-size: 12px;">-${{ number_format($discount, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Tax</span>
                <span style="font-family: 'Space Mono'; font-size: 12px;">${{ number_format($tax, 2) }}</span>
            </div>
            <div style="border-bottom: 1px dashed #cbd5e1; margin: 8px 0;"></div>
            <div class="total-row">
                <span class="total-label" style="color:#0f172a;">Total</span>
                <span class="total-amount">${{ number_format($sale->final_total, 2) }}</span>
            </div>
            <div class="total-row" style="margin-top: 5px;">
                <span class="total-label">Paid Via</span>
                <span style="font-weight: 700; font-size: 12px;">{{ $sale->payment_type === 'qr' ? 'KHQR' : strtoupper($sale->payment_type) }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="barcode"></div> <p>THANK YOU FOR SHOPPING!</p>
            <p>NO REFUNDS - EXCHANGE ONLY (7 DAYS)</p>
            <p>www.GenZPOS-system.com</p>
        </div>

    </div>

    <div class="actions">
        <a href="{{ route('pos.index') }}" class="btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <script>
        // Auto print on load (optional, remove if annoying during dev)
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>

</body>
</html>


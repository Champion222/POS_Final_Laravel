<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        /* --- GENERAL & TYPOGRAPHY --- */
        @page { margin: 0; } /* Full bleed for header/footer */
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #334155; /* Slate 700 */
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* --- LAYOUT HELPERS --- */
        .wrapper { padding: 40px; margin-top: 20px; }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* --- HEADER SECTION --- */
        .header {
            background-color: #1e1b4b; /* Indigo 950 */
            color: white;
            padding: 40px;
            height: 80px; /* Fixed height for consistency */
        }

        .brand-logo {
            float: left;
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .brand-sub {
            font-size: 10px;
            opacity: 0.7;
            font-weight: normal;
            margin-top: 2px;
            display: block;
        }

        .report-meta {
            float: right;
            text-align: right;
        }

        .meta-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            margin-bottom: 2px;
            display: block;
        }

        .meta-val {
            font-size: 13px;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        /* --- KPI CARDS (Table Layout for PDF Safety) --- */
        .kpi-table {
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 0;
            border-collapse: separate;
            border-spacing: 10px 0; /* Horizontal spacing */
            margin-left: -10px; /* Offset for spacing */
        }

        .kpi-box {
            background-color: #f8fafc; /* Slate 50 */
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            width: 33.33%;
        }

        .kpi-box.primary { border-bottom: 3px solid #4f46e5; }
        .kpi-box.success { border-bottom: 3px solid #10b981; }
        .kpi-box.info { border-bottom: 3px solid #3b82f6; }

        .kpi-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .kpi-value {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 5px;
        }

        /* --- DATA TABLE --- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            text-align: left;
            background-color: #f1f5f9;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 12px 10px;
            border-bottom: 2px solid #cbd5e1;
        }

        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            color: #334155;
        }

        .data-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* --- BADGES --- */
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-qr { background-color: #f3e8ff; color: #7e22ce; } /* Purple */
        .badge-cash { background-color: #ecfdf5; color: #047857; } /* Green */

        /* --- UTILS --- */
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="brand-logo">
            NEXPOX <span style="color: #818cf8;">Mart</span>
            <span class="brand-sub">Sales Performance Report</span>
        </div>
        
        <div class="report-meta">
            <span class="meta-label">Date Range</span>
            <span class="meta-val">{{ $dateRange }}</span>
            
            <span class="meta-label">Generated On</span>
            <span class="meta-val">{{ now()->format('M d, Y h:i A') }}</span>
        </div>
    </div>

    <div class="wrapper">
        
        <table class="kpi-table">
            <tr>
                <td class="kpi-box primary">
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value">${{ number_format($totalRevenue, 2) }}</div>
                </td>
                <td class="kpi-box success">
                    <div class="kpi-label">Transactions</div>
                    <div class="kpi-value">{{ $totalTransactions }}</div>
                </td>
                <td class="kpi-box info">
                    <div class="kpi-label">Avg. Order Value</div>
                    <div class="kpi-value">${{ number_format($avgOrderValue, 2) }}</div>
                </td>
            </tr>
        </table>

        <h3 style="margin-bottom: 10px; color: #1e293b; font-size: 14px; text-transform: uppercase; border-left: 4px solid #4f46e5; padding-left: 10px;">Transaction Details</h3>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Invoice ID</th>
                    <th style="width: 25%;">Timestamp</th>
                    <th style="width: 20%;">Cashier</th>
                    <th style="width: 15%;">Method</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td class="font-mono">#{{ $sale->invoice_number }}</td>
                    <td>
                        {{ $sale->created_at->format('M d, Y') }}
                        <br>
                        <span style="color: #94a3b8; font-size: 9px;">{{ $sale->created_at->format('h:i A') }}</span>
                    </td>
                    <td>{{ $sale->cashier->name ?? 'System' }}</td>
                    <td>
                        @if(strtolower($sale->payment_type) == 'qr')
                            <span class="badge badge-qr">KHQR Pay</span>
                        @else
                            <span class="badge badge-cash">Cash</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        ${{ number_format($sale->final_total, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <div class="footer">
        CONFIDENTIAL DOCUMENT &bull; Generated by NEXPOX System &bull; Page 1 of 1
    </div>

</body>
</html>

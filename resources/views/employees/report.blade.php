<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - {{ $employee->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Base Reset & Typography */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f4f6; /* Light gray background for screen */
            margin: 0;
            padding: 40px;
            color: #1e293b;
            -webkit-print-color-adjust: exact; /* Ensures colors print correctly */
            print-color-adjust: exact;
        }

        /* The Paper/Page Container */
        .report-container {
            max-width: 850px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); /* Nice floaty shadow */
            overflow: hidden;
            position: relative;
        }

        /* Decorative Top Bar */
        .brand-stripe {
            height: 8px;
            background: linear-gradient(90deg, #4f46e5 0%, #818cf8 100%); /* Indigo Gradient */
            width: 100%;
        }

        /* Header Section */
        .header {
            padding: 40px 40px 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #f1f5f9;
        }

        .header-title h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #1e1b4b; /* Dark Indigo */
            letter-spacing: -0.5px;
        }

        .header-title p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .company-info {
            text-align: right;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Employee Meta Card */
        .meta-section {
            padding: 30px 40px;
            background-color: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar-circle {
            width: 50px;
            height: 50px;
            background-color: #e0e7ff; /* Light Indigo */
            color: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
        }

        .user-details h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #334155;
        }

        .user-role {
            display: inline-block;
            margin-top: 4px;
            padding: 4px 10px;
            background-color: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
        }

        .report-meta {
            text-align: right;
        }

        .meta-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .meta-value {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 10px;
        }

        /* Summary Cards (The "Cool" Part) */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 0 40px;
            margin-top: 30px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .summary-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .icon-revenue { background: #ecfdf5; color: #10b981; }
        .icon-count { background: #eff6ff; color: #3b82f6; }

        .summary-data p { margin: 0; font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .summary-data h3 { margin: 2px 0 0; font-size: 24px; font-weight: 800; color: #1e293b; }

        /* Data Table */
        .table-container {
            padding: 30px 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            text-align: left;
            padding: 12px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            letter-spacing: 0.5px;
        }

        tbody td {
            padding: 14px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: -0.5px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-qr { background: #f3e8ff; color: #7e22ce; }
        .badge-cash { background: #ecfdf5; color: #047857; }

        .amount-cell {
            text-align: right;
            font-weight: 700;
            color: #1e293b;
        }

        /* Footer */
        .footer {
            background-color: #1e1b4b;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Print Button */
        .print-btn-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
        }

        .print-btn {
            background-color: #1e1b4b;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(30, 27, 75, 0.4);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            background-color: #312e81;
        }

        /* Print Styles */
        @media print {
            body { padding: 0; background-color: white; }
            .report-container { box-shadow: none; border-radius: 0; max-width: 100%; }
            .print-btn-container { display: none; }
            .header-title h1 { color: #000; }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>

    <div class="report-container">
        <div class="brand-stripe"></div>

        <div class="header">
            <div class="header-title">
                <h1>Performance Report</h1>
                <p>Sales Analysis & Staff Activity</p>
            </div>
            <div class="company-info">
                <i class="fas fa-cubes"></i> GenZPOS<br>
                Report #{{ strtoupper(uniqid()) }}
            </div>
        </div>

        <div class="meta-section">
            <div class="user-profile">
                <div class="avatar-circle">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <div class="user-details">
                    <h2>{{ $employee->name }}</h2>
                    <span class="user-role">{{ ucfirst($employee->position->name ?? 'Staff') }}</span>
                </div>
            </div>
            <div class="report-meta">
                <span class="meta-label">Reporting Period</span>
                <span class="meta-value">{{ ucfirst($filter ?? 'All Time') }}</span>
                
                <span class="meta-label">Generated On</span>
                <span class="meta-value">{{ now()->format('M d, Y â€¢ h:i A') }}</span>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-icon icon-revenue">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="summary-data">
                    <p>Total Revenue</p>
                    <h3>${{ number_format($totalSales, 2) }}</h3>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon icon-count">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="summary-data">
                    <p>Transactions</p>
                    <h3>{{ $totalTransactions }}</h3>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Timestamp</th>
                        <th>Method</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                    <tr>
                        <td class="font-mono">
                            <span style="color:#64748b;">#</span>{{ $sale->invoice_number }}
                        </td>
                        <td>
                            {{ $sale->created_at->format('M d, Y') }} 
                            <span style="color:#94a3b8; font-size:11px; margin-left:5px;">{{ $sale->created_at->format('h:i A') }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $sale->payment_type == 'qr' ? 'badge-qr' : 'badge-cash' }}">
                                {{ strtoupper($sale->payment_type) }}
                            </span>
                        </td>
                        <td class="amount-cell">
                            ${{ number_format($sale->final_total, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                            No sales records found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">
            CONFIDENTIAL DOCUMENT â€¢ Generated by GenZPOS System â€¢ {{ now()->year }}
        </div>
    </div>

</body>
</html>


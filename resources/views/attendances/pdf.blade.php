<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #0f172a;
            padding: 24px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }
        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 16px;
        }
        .summary td {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        .summary .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            display: block;
        }
        .summary .value {
            font-size: 16px;
            font-weight: 700;
            margin-top: 4px;
            display: block;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table th {
            background: #4f46e5;
            color: #ffffff;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.data-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .status-present { color: #059669; font-weight: 700; }
        .status-late { color: #d97706; font-weight: 700; }
        .status-absent { color: #dc2626; font-weight: 700; }
        .footer {
            margin-top: 16px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Attendance Report</div>
        <div class="subtitle">
            {{ $rangeLabel }} • {{ $rangeDescription }}
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Employees</span>
                <span class="value">{{ $stats['employees'] }}</span>
            </td>
            <td>
                <span class="label">Present</span>
                <span class="value" style="color:#059669;">{{ $stats['present'] }}</span>
            </td>
            <td>
                <span class="label">Late</span>
                <span class="value" style="color:#d97706;">{{ $stats['late'] }}</span>
            </td>
            <td>
                <span class="label">Absent</span>
                <span class="value" style="color:#dc2626;">{{ $stats['absent'] }}</span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $record)
            <tr>
                <td>{{ $record->user->name }}</td>
                <td>{{ $record->date?->format('d/m/Y') }}</td>
                <td>{{ $record->checkin_time?->format('h:i A') ?? '-' }}</td>
                <td>{{ $record->checkout_time?->format('h:i A') ?? '-' }}</td>
                <td>
                    @if($record->status === 'present')
                        <span class="status-present">Present</span>
                    @elseif($record->status === 'late')
                        <span class="status-late">Late</span>
                    @elseif($record->status === 'absent')
                        <span class="status-absent">Absent</span>
                    @else
                        {{ ucfirst($record->status) }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('M d, Y') }} by NEXPOX.
    </div>
</body>
</html>

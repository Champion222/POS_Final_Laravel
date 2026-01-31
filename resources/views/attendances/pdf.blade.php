<!DOCTYPE html>
<html lang="km">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attendance Report</title>
    <style>
        /* Define Fonts - Ensure you have these fonts or use Google Fonts links if supported */
        @font-face {
            font-family: 'Battambang';
            src: url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap');
        }
        @font-face {
            font-family: 'Moul';
            src: url('https://fonts.googleapis.com/css2?family=Moul&display=swap');
        }

        body {
            font-family: 'Battambang', 'Garuda', sans-serif; /* Garuda is often a fallback for Khmer in Linux */
            padding: 20px;
            color: #1f2937;
        }

        /* Header Design */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }
        .title {
            font-family: 'Moul', sans-serif;
            font-size: 24px;
            color: #1e1b4b;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        /* Stats Cards in PDF */
        .summary {
            width: 100%;
            margin-bottom: 30px;
        }
        .summary td {
            width: 33%;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            display: block;
        }
        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
        }

        /* Table Design */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table.data-table th {
            background-color: #4f46e5;
            color: white;
            padding: 10px;
            text-align: left;
            font-family: 'Moul', sans-serif; /* Khmer header font */
        }
        table.data-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Status Colors (Simulated with text color for PDF) */
        .status-present { color: #059669; font-weight: bold; }
        .status-late { color: #d97706; font-weight: bold; }
        .status-absent { color: #dc2626; font-weight: bold; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">របាយការណ៍វត្តមានបុគ្គលិក</div> <div class="subtitle">NEXPOX Mart System • ប្រចាំខែ {{ $month_label }} ឆ្នាំ {{ $year }}</div>
    </div>

    <table class="summary" cellspacing="10">
        <tr>
            <td>
                <span class="stat-label">វត្តមាន (Present)</span>
                <span class="stat-value" style="color: #059669;">{{ $total_present }}</span>
            </td>
            <td>
                <span class="stat-label">យឺត (Late)</span>
                <span class="stat-value" style="color: #d97706;">{{ $total_late }}</span>
            </td>
            <td>
                <span class="stat-label">អវត្តមាន (Absent)</span>
                <span class="stat-value" style="color: #dc2626;">{{ $total_absent }}</span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>ឈ្មោះបុគ្គលិក</th> <th>កាលបរិច្ឆេទ</th> <th>ចូល</th> <th>ចេញ</th> <th>ស្ថានភាព</th> </tr>
        </thead>
        <tbody>
            @foreach($attendances as $record)
            <tr>
                <td style="font-weight: bold;">{{ $record->employee->name }}</td>
                <td>{{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}</td>
                <td>
                    @if($record->check_out)
                        {{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($record->status == 'present')
                        <span class="status-present">វត្តមាន</span>
                    @elseif($record->status == 'late')
                        <span class="status-late">យឺត</span>
                    @elseif($record->status == 'absent')
                        <span class="status-absent">អវត្តមាន</span>
                    @else
                        {{ $record->status }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        ឯកសារនេះត្រូវបានបង្កើតដោយប្រព័ន្ធ NEXPOX នៅថ្ងៃទី {{ now()->format('d/m/Y') }}
    </div>

</body>
</html>

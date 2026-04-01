<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Income Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background: linear-gradient(to right, #14b8a6, #06b6d4);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            padding: 25px;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 12px;
            color: #4a5568;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #1a202c;
        }

        .period-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px;
        }

        .period-table th {
            background-color: #0f766e;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .period-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .period-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .period-table tr:hover {
            background-color: #edf2f7;
        }

        .total-row {
            font-weight: bold;
            background-color: #f1f5f9 !important;
        }

        .total-row td {
            border-top: 2px solid #0f766e;
            border-bottom: 2px solid #0f766e;
        }

        .breakdown-section {
            margin: 30px;
        }

        .breakdown-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #0f766e;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 11px;
            border-top: 1px solid #e2e8f0;
            margin-top: 20px;
        }

        .currency {
            text-align: right;
            font-family: monospace;
        }

        .highlight {
            color: #0f766e;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>CASA ORO INCOME REPORT</h1>
        <p>{{ $data['period']['label'] }} ({{ $data['period']['start'] }} to {{ $data['period']['end'] }})</p>
        <p>Report Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Income</div>
            <div class="summary-value highlight">₱{{ number_format($data['summary']['total_income'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Rent Income</div>
            <div class="summary-value">₱{{ number_format($data['summary']['total_rent'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Electric Income</div>
            <div class="summary-value" style="color: #d97706;">
                ₱{{ number_format($data['summary']['total_electric'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Payments</div>
            <div class="summary-value">{{ number_format($data['summary']['payment_count']) }}</div>
        </div>
    </div>

    <table class="period-table">
        <thead>
            <tr>
                <th>Period</th>
                <th class="currency">Total Income</th>
                <th class="currency">Rent Income</th>
                <th class="currency">Electric Income</th>
                <th>Payments</th>
                <th class="currency">Avg/ Payment</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['period_data'] as $period)
                <tr>
                    <td>{{ $period['label'] }}</td>
                    <td class="currency highlight">₱{{ number_format($period['total_income'], 2) }}</td>
                    <td class="currency">₱{{ number_format($period['rent_income'], 2) }}</td>
                    <td class="currency" style="color: #d97706;">₱{{ number_format($period['electric_income'], 2) }}
                    </td>
                    <td>{{ $period['payment_count'] }}</td>
                    <td class="currency">
                        ₱{{ number_format($period['total_income'] / ($period['payment_count'] ?: 1), 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="currency highlight">
                    <strong>₱{{ number_format($data['summary']['total_income'], 2) }}</strong>
                </td>
                <td class="currency"><strong>₱{{ number_format($data['summary']['total_rent'], 2) }}</strong></td>
                <td class="currency" style="color: #d97706;">
                    <strong>₱{{ number_format($data['summary']['total_electric'], 2) }}</strong>
                </td>
                <td><strong>{{ $data['summary']['payment_count'] }}</strong></td>
                <td class="currency"><strong>₱{{ number_format($data['summary']['avg_per_payment'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="breakdown-section">
        <div class="breakdown-title">Top Rooms by Income</div>
        <table class="period-table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th class="currency">Total Income</th>
                    <th>Payments</th>
                    <th class="currency">Avg/ Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['room_breakdown']->take(10) as $room)
                    <tr>
                        <td>Room {{ $room['room_number'] }}</td>
                        <td class="currency highlight">₱{{ number_format($room['total_income'], 2) }}</td>
                        <td>{{ $room['payment_count'] }}</td>
                        <td class="currency">₱{{ number_format($room['avg_per_payment'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="breakdown-section">
        <div class="breakdown-title">Top Tenants by Income</div>
        <table class="period-table">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th class="currency">Total Income</th>
                    <th>Payments</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['tenant_breakdown']->take(10) as $tenant)
                    <tr>
                        <td>{{ $tenant['tenant_name'] }}</td>
                        <td class="currency highlight">₱{{ number_format($tenant['total_income'], 2) }}</td>
                        <td>{{ $tenant['payment_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Casa Oro Property Management • {{ config('app.url') }} • {{ now()->format('Y') }}</p>
        <p>This is a system-generated report. For inquiries, contact management@casaoro.com</p>
    </div>
</body>

</html>

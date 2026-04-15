<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: 80mm 150mm;

            margin: 0;
        }

        body {
            font-family: 'Courier', 'Helvetica', sans-serif;
            margin: 0;
            padding: 10mm 5mm;
            width: 70mm;
            color: #000;
            font-size: 9px;
            line-height: 1.2;
        }

        .peso {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .header {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }

        .logo {
            max-width: 40mm;
            height: auto;
            margin-bottom: 4px;
        }

        .receipt-status {
            background: #000;
            color: #fff;
            padding: 2px 0;
            margin: 5px 0;
            font-size: 10px;
            letter-spacing: 1px;
        }

        .w-full {
            width: 100%;
        }

        .info-table {
            margin: 8px 0;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .label {
            width: 35%;
            color: #444;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .divider-dashed {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .amount-row td {
            padding: 4px 0;
            font-size: 10px;
        }

        .total-box {
            border: 1px solid #000;
            padding: 5px;
            margin-top: 5px;
            background-color: #f8f9fa;
            border-radius: 2px;
        }

        .electric-note {
            font-size: 7px;
            color: #555;
            padding-left: 5px;
            margin-top: 1px;
            line-height: 1.1;
        }

        .footer {
            margin-top: 15px;
            font-size: 8px;
        }

        .section-title {
            font-weight: bold;
            font-size: 8px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 0 2px;
            border-bottom: 1px dashed #ddd;
            margin: 6px 0 4px;
        }

        .debt-text {
            color: #e11d48;
        }
    </style>
</head>

<body>
    @php
        $payment = $receipt->leasePayment;
        $tenant = $payment->lease->tenant;

        $baseRent = $payment->amount;
        $electricShare = $payment->electric_bill_amount ?? 0;
        $previousDebt = $payment->carried_over_debt ?? 0;

        $calculatedTotal = $baseRent + $electricShare + $previousDebt;
        $displayTotal = $calculatedTotal;

        $p = '<span class="peso">&#8369;</span>';

        $roomTotalBill = 0;
        $tenantCount = 0;
        $billingMonth = $payment->due_date->format('F Y');
        $debtDateRange = '';

        $electricConsumption = '';
        $electricDays = 0;
        
        if ($payment->electricBill) {
            $roomTotalBill = $payment->electricBill->total_amount;
            $bill = $payment->electricBill;
            $billingMonth = $bill->billing_month;
            $billingStart = $billingMonth->copy()->startOfMonth();
            $billingEnd = $billingMonth->copy()->endOfMonth();
            
            // Count active tenants during billing month
            $activeLeases = \App\Models\Lease::where('room_id', $payment->lease->room_id)
                ->where('start_date', '<=', $billingEnd)
                ->where('end_date', '>=', $billingStart)
                ->get();
            
            $totalBedDays = 0;
            $thisLeaseDays = 0;
            
            foreach ($activeLeases as $al) {
                $ls = \Carbon\Carbon::parse($al->start_date)->max($billingStart);
                $le = \Carbon\Carbon::parse($al->end_date)->min($billingEnd);
                $days = (int) $ls->diffInDays($le) + 1;
                if ($days > 0) {
                    $totalBedDays += $days;
                    if ($al->id === $payment->lease->id) {
                        $thisLeaseDays = $days;
                        $electricDays = $days;
                        $electricConsumption = $ls->format('M d') . ' - ' . $le->format('M d') . ' (' . $days . ' days)';
                    }
                }
            }
            
            $tenantCount = $totalBedDays > 0 ? $activeLeases->count() : 1;

            // For carried debt display
            if ($previousDebt > 0) {
                $prevMonthObj = $billingMonth->copy()->subMonth();
                $leaseStart = \Carbon\Carbon::parse($payment->lease->start_date)->startOfDay();
                $leaseEnd = \Carbon\Carbon::parse($payment->lease->end_date)->startOfDay();
                $debtStart = $leaseStart->max($prevMonthObj->copy()->startOfMonth());
                $debtEnd = $leaseEnd->min($prevMonthObj->copy()->endOfMonth());
                $debtDateRange = $debtStart->format('M d, Y') . ' – ' . $debtEnd->format('M d, Y');
            }
        }
    @endphp

    <div class="header text-center">
        @if (file_exists(public_path('images/casa_oro_logo.png')))
            <img src="{{ public_path('images/casa_oro_logo.png') }}" class="logo">
        @else
            <h2 style="margin:0; font-size: 18px;">CASA ORO</h2>
        @endif
        <div class="receipt-status bold">OFFICIAL RECEIPT</div>
        <p style="margin:2px 0; font-size: 8px;">Property Management Service</p>
    </div>

    <table class="w-full info-table">
        <tr>
            <td class="label">REF NO:</td>
            <td class="bold">{{ $receipt->receipt_number }}</td>
        </tr>
        <tr>
            <td class="label">DATE:</td>
            <td>{{ $receipt->created_at->format('d M Y | g:i A') }}</td>
        </tr>
        <tr>
            <td class="label">TENANT:</td>
            <td class="bold">{{ strtoupper($tenant->full_name) }}</td>
        </tr>
        <tr>
            <td class="label">TENANT ID:</td>
            <td>{{ $tenant->id }}</td>
        </tr>
        <tr>
            <td class="label">UNIT:</td>
            <td class="bold">Room {{ $payment->lease->room->room_number }}</td>
        </tr>
        <tr>
            <td class="label">PERIOD:</td>
            <td class="bold">{{ $billingMonth }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="section-title">Payment Breakdown</div>

    <table class="w-full info-table">
        <tr class="amount-row">
            <td>Monthly Rent</td>
            <td class="text-right bold">{!! $p !!}{{ number_format($baseRent, 2) }}</td>
        </tr>

        @if ($electricShare > 0)
            <tr class="amount-row">
                <td>Electric Share</td>
                <td class="text-right bold">{!! $p !!}{{ number_format($electricShare, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="electric-note">
                    Used: {{ $electricConsumption }}<br>
                    Room: {!! $p !!}{{ number_format($roomTotalBill, 2) }} ÷ {{ $totalBedDays }} days
                </td>
            </tr>
        @endif

        @if ($previousDebt > 0)
            <tr class="amount-row">
                <td class="debt-text">Previous Balance</td>
                <td class="text-right bold debt-text">{!! $p !!}{{ number_format($previousDebt, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="electric-note">
                    For consumption period: {{ $debtDateRange }}
                </td>
            </tr>
        @endif
    </table>

    <div class="total-box">
        <table class="w-full">
            <tr>
                <td class="bold" style="font-size: 11px;">TOTAL PAID:</td>
                <td class="text-right bold" style="font-size: 14px;">
                    {!! $p !!}{{ number_format($displayTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    <table class="w-full info-table" style="margin-top: 8px;">
        <tr>
            <td class="label">METHOD:</td>
            <td class="text-right bold" style="text-transform: uppercase;">
                {{ $receipt->payment_method }}
            </td>
        </tr>
    </table>

    <div class="divider-dashed"></div>

    <div class="footer text-center">
        <p style="font-size: 8px; margin: 3px 0; line-height: 1.3;">
            Thank you for your payment.<br>
            <span style="font-weight: normal;">
                Rent: {!! $p !!}{{ number_format($baseRent, 2) }} |
                Electric: {!! $p !!}{{ number_format($electricShare + $previousDebt, 2) }}<br>
                <strong style="font-size: 9px;">TOTAL SETTLED:
                    {!! $p !!}{{ number_format($displayTotal, 2) }}</strong>
            </span>
        </p>
        <p style="font-size: 7px; margin-top: 3px; color: #666; border-top: 1px dashed #eee; padding-top: 4px;">
            Casa Oro Property Management
        </p>
    </div>
</body>

</html>

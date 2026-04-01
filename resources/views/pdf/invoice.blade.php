<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 11px;
            letter-spacing: -0.2px;
        }

        .wrapper {
            padding: 50px;
        }

        .w-full {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .v-top {
            vertical-align: top;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .header-table {
            margin-bottom: 40px;
        }

        .logo {
            height: 45px;
            margin-bottom: 10px;
        }

        .invoice-label {
            font-size: 32px;
            font-weight: 300;
            color: #0f172a;
            margin: 0;
            letter-spacing: 2px;
        }

        .status-badge {
            border: 1px solid #1e3a8a;
            color: #1e3a8a;
            padding: 2px 12px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            margin-top: 8px;
            display: inline-block;
        }

        .section-label {
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            display: inline-block;
            width: 100%;
        }

        .items-table {
            margin-bottom: 30px;
        }

        .items-table th {
            text-align: left;
            font-size: 10px;
            color: #64748b;
            border-bottom: 1px solid #0f172a;
            padding: 12px 5px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 18px 5px;
            border-bottom: 1px solid #f1f5f9;
        }

        .formula-box {
            border-left: 3px solid #1e3a8a;
            background-color: #f8fafc;
            padding: 20px;
            margin-top: 40px;
        }

        .totals-table {
            width: 320px;
            float: right;
            margin-top: 20px;
        }

        .footer {
            clear: both;
            margin-top: 50px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        .description-main {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .description-sub {
            color: #64748b;
            line-height: 1.4;
        }

        .price-col {
            text-align: right;
            font-weight: 700;
        }

        .debt-highlight {
            color: #e11d48;
            font-weight: 700;
        }

        .stay-badge {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
        }

        .move-out-alert {
            color: #e11d48;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .calc-table {
            margin-top: 15px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }

        .calc-table td {
            padding: 8px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
        }

        .calc-header {
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @php
        $baseRent = $payment->amount ?? 0;
        $electricShare = $payment->electric_bill_amount ?? 0;
        $previousDebt = $payment->carried_over_debt ?? 0;
        $totalDue = $baseRent + $electricShare + $previousDebt;

        $hasElectricBill = $payment->electric_bill_id;
        $electricBill = $hasElectricBill ? $payment->electricBill : null;
        $p = '&#8369;';

        if ($hasElectricBill && $electricBill) {
            $monthStart = \Carbon\Carbon::parse($electricBill->billing_month)->startOfMonth();
            $monthEnd = \Carbon\Carbon::parse($electricBill->billing_month)->endOfMonth();

            $totalRoomBedDays = 0;
            $activeTenantsCount = $electricBill->leasePayments->count();

            foreach ($electricBill->leasePayments as $rp) {
                $tStart = \Carbon\Carbon::parse($rp->lease->start_date)->startOfDay()->max($monthStart);
                $tEnd = \Carbon\Carbon::parse($rp->lease->end_date)->startOfDay()->min($monthEnd);
                $totalRoomBedDays += (int) $tStart->diffInDays($tEnd) + 1;
            }

            $leaseStart = \Carbon\Carbon::parse($payment->lease->start_date)->startOfDay();
            $leaseEnd = \Carbon\Carbon::parse($payment->lease->end_date)->startOfDay();
            $stayStart = $leaseStart->max($monthStart);
            $stayEnd = $leaseEnd->min($monthEnd);
            $myDays = (int) $stayStart->diffInDays($stayEnd) + 1;

            $sharePercentage = $totalRoomBedDays > 0 ? ($myDays / $totalRoomBedDays) * 100 : 0;
            $isMovingOut = $leaseEnd->between($monthStart, $monthEnd);

            $prevMonthObj = $electricBill->billing_month->copy()->subMonth();
            $debtStart = $leaseStart->max($prevMonthObj->copy()->startOfMonth());
            $debtEnd = $leaseEnd->min($prevMonthObj->copy()->endOfMonth());
            $debtDateRange = $debtStart->format('M d, Y') . ' – ' . $debtEnd->format('M d, Y');
        }
    @endphp

    <div class="wrapper">
        <table class="w-full header-table">
            <tr>
                <td class="v-top">
                    <img src="{{ public_path('images/casa_oro_logo.png') }}" class="logo">
                    <div style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Casa Oro</div>
                </td>
                <td class="text-right v-top">
                    <h1 class="invoice-label">INVOICE</h1>
                    <div style="font-weight: 400; margin-top: 5px;">Ref. #{{ $invoice->invoice_number }}</div>
                    <div class="status-badge">{{ ucfirst($invoice->status) }}</div>
                </td>
            </tr>
        </table>

        <table class="w-full info-table">
            <tr>
                <td class="v-top" width="35%">
                    <div class="section-label">From</div>
                    <strong>Casa Oro Management</strong><br>
                    <span style="color: #64748b;">Manila, Philippines</span>
                </td>
                <td class="v-top" width="35%">
                    <div class="section-label">Bill To</div>
                    <strong>{{ $payment->lease->tenant->full_name }}</strong><br>
                    <span style="color: #64748b;">
                        Unit R{{ $payment->lease->room->room_number }}<br>
                        Tenant ID: {{ $payment->lease->tenant->id }}
                    </span>
                </td>
                <td class="v-top text-right" width="30%">
                    <div class="section-label">Timeline</div>
                    <div style="margin-bottom: 4px;">Issued: {{ now()->format('d M Y') }}</div>
                    <div style="font-weight: 700;">Due Date: {{ $payment->due_date->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Service Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="description-main">Monthly Rent — {{ $payment->due_date->format('F Y') }}</div>
                        <div class="description-sub">Room {{ $payment->lease->room->room_number }}</div>
                    </td>
                    <td class="price-col">{!! $p !!}{{ number_format($baseRent, 2) }}</td>
                </tr>

                @if ($hasElectricBill)
                    <tr>
                        <td>
                            <div class="description-main">Utility Consumption — Electricity</div>
                            <div class="description-sub" style="margin-top: 5px;">
                                @if ($electricShare > 0)
                                    <strong>Billing Period:</strong>
                                    {{ $electricBill->billing_month->format('F Y') }}<br>
                                    <strong>Calculated Stay:</strong> {{ $stayStart->format('M d, Y') }} –
                                    {{ $stayEnd->format('M d, Y') }}
                                    <span class="stay-badge">{{ $myDays }} Day(s)</span>

                                    @if ($isMovingOut)
                                        <br><span class="move-out-alert">Notice: Pro-rated for Move-out</span>
                                    @endif
                                @else
                                    <span style="font-style: italic; color: #1e3a8a;">Advance Rent: Utility share
                                        deferred.</span>
                                @endif

                                @if ($previousDebt > 0)
                                    <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0;">
                                        <span class="debt-highlight">+ Carry-over Balance:
                                            {!! $p !!}{{ number_format($previousDebt, 2) }}</span>
                                        <br>
                                        <small style="color: #64748b; font-style: italic;">For consumption period:
                                            {{ $debtDateRange }}</small>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="price-col">
                            {!! $p !!}{{ number_format($electricShare + $previousDebt, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($hasElectricBill && $electricShare > 0)
            <div style="width: 50%; float: left; margin-top: 20px;">
                <div class="section-label">Sharing Breakdown</div>
                <table class="calc-table">
                    <tr>
                        <td class="calc-header">Room Total Bill</td>
                        <td class="text-right">
                            {!! $p !!}{{ number_format($electricBill->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="calc-header">Total Room Occupancy</td>
                        <td class="text-right">{{ $activeTenantsCount }} Tenant(s)</td>
                    </tr>
                    <tr>
                        <td class="calc-header">Total Room Bed-Days</td>
                        <td class="text-right">{{ $totalRoomBedDays }} Days</td>
                    </tr>
                    <tr>
                        <td class="calc-header">Your Occupancy</td>
                        <td class="text-right">{{ $myDays }} Days ({{ number_format($sharePercentage, 1) }}%)
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <table class="totals-table">
            <tr>
                <td style="padding: 8px 0; color: #64748b;">SUBTOTAL RENT</td>
                <td style="padding: 8px 0; text-align: right;">{!! $p !!}{{ number_format($baseRent, 2) }}
                </td>
            </tr>
            @if ($electricShare > 0)
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">UTILITY SHARE</td>
                    <td style="padding: 8px 0; text-align: right;">
                        {!! $p !!}{{ number_format($electricShare, 2) }}</td>
                </tr>
            @endif
            @if ($previousDebt > 0)
                <tr>
                    <td style="padding: 8px 0; color: #e11d48; font-weight: bold;">PREVIOUS DEBT</td>
                    <td style="padding: 8px 0; text-align: right; color: #e11d48; font-weight: bold;">
                        {!! $p !!}{{ number_format($previousDebt, 2) }}</td>
                </tr>
            @endif
            <tr style="border-top: 2px solid #0f172a;">
                <td style="font-size: 18px; font-weight: 700; color: #0f172a; padding-top: 15px;">TOTAL DUE</td>
                <td style="font-size: 18px; font-weight: 700; color: #0f172a; padding-top: 15px; text-align: right;">
                    {!! $p !!}{{ number_format($totalDue, 2) }}
                </td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        @if ($hasElectricBill && $electricBill)
            <div class="formula-box">
                <div style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 9px;">Transparency
                    Notice</div>
                <div style="margin-top: 10px; font-size: 10px; color: #475569; line-height: 1.5;">
                    • <strong>Sharing Logic:</strong> Your share is `(Your Days / Total Room Bed-Days) × Room Total`.
                    This ensures you only pay for the exact time you were in the building relative to your
                    roommates.<br>
                    • <strong>Cycle Occupants:</strong> {{ $activeTenantsCount }} tenants shared this room's resources
                    during the {{ $electricBill->billing_month->format('F Y') }} period.
                </div>
            </div>
        @endif

        <div class="footer">
            <p>Casa Oro Management <br>For billing inquiries, please contact management.
            </p>
        </div>
    </div>
</body>

</html>

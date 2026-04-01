<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Lease Agreement - {{ $lease->tenant->full_name }}</title>
    <style>
        @page {
            margin: 0.5cm;

            size: A4;
        }

        body {
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            line-height: 1.4;

            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .wrapper {
            padding: 40px 50px;
            position: relative;
            min-height: 1000px;

        }


        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo {
            height: 60px;

            width: auto;
            margin-bottom: 10px;
        }

        .brand-meta {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #4f46e5;
            margin-bottom: 4px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: 1px;
        }


        .parties-grid {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }

        .party-box {
            width: 50%;
            vertical-align: top;
        }

        .label {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }


        .content-section {
            margin-bottom: 15px;
            font-size: 13px;
        }

        .highlight-text {
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
        }


        .financial-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 10px;
        }

        .financial-label {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            color: #4f46e5;
        }

        .financial-value {
            font-size: 18px;
            font-weight: bold;
            color: #4338ca;
        }


        .declaration {
            background: #0f172a;
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-style: italic;
            font-size: 12px;
            border-left: 6px solid #4f46e5;
        }


        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .sig-col {
            width: 45%;
            text-align: center;
        }

        .sig-space {
            height: 40px;
            vertical-align: bottom;
            font-style: italic;
            color: #cbd5e1;
            font-size: 12px;
        }

        .sig-line {
            border-top: 1px solid #0f172a;
            margin-top: 5px;
            padding-top: 5px;
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
        }


        .footer {
            position: absolute;
            bottom: 30px;
            left: 50px;
            right: 50px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
            color: #94a3b8;
            font-size: 9px;
            font-family: 'Helvetica', sans-serif;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ public_path('images/casa_oro_logo.png') }}" alt="Casa Oro Residences" class="logo">
            <div class="brand-meta">Contract of Lease</div>
            <h1 class="title">LEASE AGREEMENT</h1>
        </div>

        <div class="content-section">
            <p>This Lease Agreement ("Agreement") is made and entered into on
                <span class="highlight-text">{{ now()->format('F d, Y') }}</span>, by and between:
            </p>
        </div>

        <table class="parties-grid">
            <tr>
                <td class="party-box">
                    <div class="label">Landlord</div>
                    <div class="value">CASA ORO RESIDENCES</div>
                </td>
                <td class="party-box">
                    <div class="label">Tenant</div>
                    <div class="value uppercase">{{ strtoupper($lease->tenant->full_name) }}</div>
                </td>
            </tr>
        </table>

        <div class="content-section">
            <p><strong>Property:</strong> The Landlord leases to the Tenant the premises identified as
                <span class="highlight-text">UNIT {{ $lease->room->room_number }}</span>, located at Casa Oro.
            </p>

            <p><strong>Lease Term:</strong> The duration of this agreement shall be for
                <strong>{{ strtoupper($lease->duration_display) }}</strong>,
                beginning on <span class="highlight-text">{{ $lease->start_date->format('F d, Y') }}</span>
                and concluding on <span class="highlight-text">{{ $lease->end_date->format('F d, Y') }}</span>.
            </p>
        </div>

        <div class="financial-box">
            <div class="financial-label">Financial Obligation</div>
            <div class="financial-value">
                {{ number_format($lease->monthly_rent, 2) }} PESOS <span
                    style="font-size: 11px; color: #64748b; font-weight: normal;">/ Monthly</span>
            </div>
            <p style="margin-top: 5px; font-size: 11px; color: #64748b;">Payable on or before the 5th day of each
                calendar month.</p>
        </div>

        <div class="declaration">
            <div class="label" style="color: #818cf8; margin-bottom: 5px;">Binding Declaration</div>
            "I, {{ strtoupper($lease->tenant->full_name) }}, hereby confirm my agreement to pay the stipulated monthly
            rent for UNIT {{ $lease->room->room_number }}. I acknowledge that I have read and understood all terms and
            conditions provided in this contract."
        </div>

        <p style="font-size: 10px; color: #94a3b8; font-style: italic; margin-top: 20px;">
            This document is electronically generated and holds legal validity under the Electronic Commerce Act of 2000
            (RA 8792) of the Republic of the Philippines.
        </p>

        <table class="signature-table">
            <tr>
                <td class="sig-col">
                    <div class="sig-space">/s/ Authorized Official</div>
                    <div class="sig-line">Casa Oro Management</div>
                </td>
                <td style="width: 10%"></td>
                <td class="sig-col">
                    <div class="sig-space" style="color: #4f46e5; font-weight: bold;">
                        {{ strtoupper($lease->tenant->full_name) }}</div>
                    <div class="sig-line" style="color: #94a3b8;">Tenant Signature</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <strong>CASA ORO RESIDENCES</strong><br>
            Generated on {{ now()->format('F d, Y g:i A') }} • ID:
            L-{{ str_pad($lease->id, 5, '0', STR_PAD_LEFT) }}<br>
            <span style="color: #cbd5e1;">Digital Verification Token:
                {{ substr(md5($lease->id . now()), 0, 16) }}</span>
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Statement of Account - {{ $tenant->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto bg-white p-10 shadow-lg border-t-8 border-black">
        <div class="flex justify-between items-start mb-10">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tighter">Statement of Account</h1>
                <p class="text-gray-500 text-xs mt-1">Generated on {{ now()->format('M d, Y') }}</p>
            </div>
            <button onclick="window.print()"
                class="no-print bg-black text-white px-4 py-2 text-xs font-bold uppercase tracking-widest rounded hover:bg-zinc-800">
                Print Statement
            </button>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-10 border-b border-gray-100 pb-10">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tenant Details</p>
                <p class="text-lg font-bold">{{ $tenant->full_name }}</p>
                <p class="text-sm text-gray-600">Room:
                    {{ $pendingPayments->first()->lease->room->room_number ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Outstanding Balance</p>
                <p class="text-3xl font-black text-rose-600">₱{{ number_format($totalDue, 2) }}</p>
            </div>
        </div>

        <table class="w-full mb-10 text-left">
            <thead>
                <tr class="border-b-2 border-black text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    <th class="py-3">Due Date</th>
                    <th class="py-3">Description</th>
                    <th class="py-3 text-right">Rent</th>
                    <th class="py-3 text-right">Utilities</th>
                    <th class="py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($pendingPayments as $payment)
                    <tr class="text-sm">
                        <td class="py-4 font-bold">{{ $payment->due_date->format('M d, Y') }}</td>
                        <td class="py-4 text-gray-600">Monthly Billing Cycle</td>
                        <td class="py-4 text-right">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="py-4 text-right">
                            ₱{{ number_format($payment->electric_bill_amount + $payment->carried_over_debt, 2) }}</td>
                        <td class="py-4 text-right font-bold">
                            ₱{{ number_format($payment->amount + $payment->electric_bill_amount + $payment->carried_over_debt, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 text-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Notice</p>
            <p class="text-xs text-gray-600">Please settle the outstanding amount immediately to avoid any disruption in
                your stay or utility services. If payment has been made, please provide a copy of the receipt.</p>
        </div>
    </div>
</body>

</html>

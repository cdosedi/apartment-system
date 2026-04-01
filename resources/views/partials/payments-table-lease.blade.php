@if ($payments->isEmpty())
    <div class="text-center py-12">
        <div class="inline-block p-4 bg-gray-100 rounded-full mb-4">
            <i class="fa-solid fa-file-invoice text-gray-400 text-3xl"></i>
        </div>
        <h4 class="text-lg font-medium text-gray-900 mb-2">
            @if ($filter === 'all')
                No payments found
            @else
                No payments for this lease period
            @endif
        </h4>
        <p class="text-gray-500">Payments appear here once a lease is created.</p>
    </div>
@else
    @php

        $getElectricData = function ($payment) {
            if (!$payment->electricBill) {
                return null;
            }

            $roomTotal = $payment->electricBill->total_amount;
            $tenantCount = $payment->electricBill->leasePayments()->count();

            $leaseStart = \Carbon\Carbon::parse($payment->lease->start_date)->startOfDay();
            $leaseEnd = \Carbon\Carbon::parse($payment->lease->end_date)->startOfDay();
            $prevMonthObj = $payment->electricBill->billing_month->copy()->subMonth();

            $debtStart = $leaseStart->max($prevMonthObj->copy()->startOfMonth());
            $debtEnd = $leaseEnd->min($prevMonthObj->copy()->endOfMonth());

            return [
                'room_total' => $roomTotal,
                'tenant_count' => $tenantCount,
                'period' => $debtStart->format('M d, Y') . ' – ' . $debtEnd->format('M d, Y'),
            ];
        };
    @endphp

    @if ($filter === 'all')
        <div class="space-y-8">
            @foreach ($leases as $lease)
                @php
                    $leasePayments = $payments->filter(fn($p) => $p->lease_id === $lease->id);
                    $unpaidPayments = $leasePayments->where('status', '!=', 'paid');
                    $unpaidCount = $unpaidPayments->count();
                    $totalAmount = $unpaidPayments->sum('amount');
                    $allHaveElectricBills = $unpaidPayments->every(fn($p) => $p->electric_bill_id !== null);

                    $missingPeriods = $unpaidPayments
                        ->filter(fn($p) => $p->electric_bill_id === null)
                        ->map(fn($p) => $p->due_date->format('M d, Y'))
                        ->values()
                        ->toArray();
                @endphp

                @if ($leasePayments->count() > 0)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900">
                                Room {{ $lease->room->room_number }}
                                <span class="text-sm text-gray-500 ml-2">
                                    ({{ $lease->start_date->format('M d, Y') }} →
                                    {{ $lease->end_date->format('M d, Y') }})
                                    @if ($lease->status === 'active')
                                        <span
                                            class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-2">Active</span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full ml-2">Expired</span>
                                    @endif
                                </span>
                            </h4>
                            @if ($unpaidCount > 0)
                                @if ($allHaveElectricBills)
                                    <button type="button"
                                        onclick="openPayAllModalForLease({{ $lease->id }}, {{ $unpaidCount }}, {{ $totalAmount }})"
                                        class="inline-flex items-center px-3 py-1 bg-gray-900 text-white text-xs rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                        <i class="fa-solid fa-money-bill-wave mr-1"></i> Pay All ({{ $unpaidCount }})
                                    </button>
                                @else
                                    <button type="button" data-missing-periods='{{ json_encode($missingPeriods) }}'
                                        onclick="showMissingElectricBillsModal({{ $lease->id }}, this)"
                                        class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-md cursor-not-allowed"
                                        title="Cannot pay until all electric bills are issued">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Missing Electric Bills
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Due Date</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Monthly Rent</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Electric Details</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Total Settled</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Status</th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Paid On</th>
                                    <th
                                        class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($leasePayments as $payment)
                                    @php $e = $getElectricData($payment); @endphp
                                    <tr class="{{ $payment->status === 'overdue' ? 'bg-red-50' : '' }}">
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-600">
                                            {{ $payment->due_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm">
                                            @if ($e)
                                                <div class="flex flex-col">
                                                    <div class="flex items-center font-bold text-blue-800">
                                                        <i class="fa-solid fa-bolt text-yellow-500 mr-1 text-xs"></i>
                                                        ₱{{ number_format($payment->electric_bill_amount, 2) }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 leading-tight">
                                                        Total Bill: ₱{{ number_format($e['room_total'], 2) }}
                                                        ({{ $e['tenant_count'] }} tenants)
                                                        <br>
                                                        Period: {{ $e['period'] }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap bg-gray-50">
                                            <div class="text-sm font-bold text-gray-900">
                                                ₱{{ number_format($payment->amount + ($payment->electric_bill_amount ?? 0) + ($payment->carried_over_debt ?? 0), 2) }}
                                            </div>
                                            @if ($payment->carried_over_debt > 0)
                                                <div class="text-[10px] text-red-600 font-medium">
                                                    + ₱{{ number_format($payment->carried_over_debt, 2) }} Previous
                                                    Balance
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : ($payment->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-600">
                                            @if ($payment->status === 'paid')
                                                {{ $payment->paid_at?->format('M d, Y') ?? 'N/A' }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            @if ($payment->status === 'paid')
                                                <a href="{{ route('payments.receipt', $payment) }}"
                                                    class="inline-flex items-center text-gray-900 hover:text-gray-700 underline"
                                                    target="_blank">
                                                    <i class="fa-solid fa-file-invoice mr-1"></i> Receipt
                                                </a>
                                            @else
                                                <a href="{{ route('payments.invoice', $payment) }}"
                                                    class="text-gray-900 hover:text-gray-700 mr-2 underline"
                                                    target="_blank">
                                                    <i class="fa-solid fa-file-invoice mr-1"></i> Invoice
                                                </a>
                                                @if ($payment->electric_bill_id)
                                                    <a href="{{ route('payments.create', $payment) }}"
                                                        class="text-gray-900 hover:text-gray-700 underline">
                                                        <i class="fa-solid fa-credit-card mr-1"></i> Pay
                                                    </a>
                                                @else
                                                    <button type="button"
                                                        onclick="showNoElectricBillModal({{ $payment->id }}, '{{ $payment->due_date->format('F Y') }}')"
                                                        class="text-yellow-700 hover:text-yellow-900 underline inline-flex items-center"
                                                        title="Electric bill not issued for this period">
                                                        <i
                                                            class="fa-solid fa-triangle-exclamation mr-1 text-yellow-500"></i>
                                                        Pay
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        @php
            $selectedLease = $payments->isNotEmpty() ? $payments->first()->lease : null;
            $unpaidPayments = $payments->where('status', '!=', 'paid');
            $unpaidCount = $unpaidPayments->count();
            $totalAmount = $unpaidPayments->sum('amount');
            $allHaveElectricBills = $unpaidPayments->every(fn($p) => $p->electric_bill_id !== null);
        @endphp

        @if ($selectedLease)
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-gray-900">
                        Room {{ $selectedLease->room->room_number }}
                        <span class="text-sm text-gray-500 ml-2">
                            ({{ $selectedLease->start_date->format('M d, Y') }} →
                            {{ $selectedLease->end_date->format('M d, Y') }})
                            @if ($selectedLease->status === 'active')
                                <span
                                    class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-2">Active</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full ml-2">Expired</span>
                            @endif
                        </span>
                    </h4>
                    @if ($unpaidCount > 0)
                        @if ($allHaveElectricBills)
                            <button type="button"
                                onclick="openPayAllModalForLease({{ $selectedLease->id }}, {{ $unpaidCount }}, {{ $totalAmount }})"
                                class="inline-flex items-center px-3 py-1 bg-gray-900 text-white text-xs rounded-md hover:bg-gray-800">
                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Pay All ({{ $unpaidCount }})
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monthly Rent</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Electric Details
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Settled</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paid On</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($payments as $payment)
                        @php $e = $getElectricData($payment); @endphp
                        <tr class="{{ $payment->status === 'overdue' ? 'bg-red-50' : '' }}">
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment->due_date->format('M d, Y') }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm">
                                @if ($e)
                                    <div class="flex flex-col">
                                        <div class="flex items-center font-bold text-blue-800">
                                            <i class="fa-solid fa-bolt text-yellow-500 mr-1 text-xs"></i>
                                            ₱{{ number_format($payment->electric_bill_amount, 2) }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 leading-tight">
                                            Total: ₱{{ number_format($e['room_total'], 2) }}
                                            ({{ $e['tenant_count'] }})
                                            <br>
                                            {{ $e['period'] }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gray-50">
                                ₱{{ number_format($payment->amount + ($payment->electric_bill_amount ?? 0) + ($payment->carried_over_debt ?? 0), 2) }}
                                @if ($payment->carried_over_debt > 0)
                                    <div class="text-[10px] text-red-600 font-medium">+
                                        ₱{{ number_format($payment->carried_over_debt, 2) }} Debt</div>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment->paid_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-right text-sm font-medium">
                                @if ($payment->status === 'paid')
                                    <a href="{{ route('payments.receipt', $payment) }}"
                                        class="text-gray-900 underline" target="_blank">Receipt</a>
                                @else
                                    <a href="{{ route('payments.invoice', $payment) }}"
                                        class="text-gray-900 underline mr-2" target="_blank">Invoice</a>
                                    <a href="{{ route('payments.create', $payment) }}"
                                        class="text-gray-900 underline">Pay</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif

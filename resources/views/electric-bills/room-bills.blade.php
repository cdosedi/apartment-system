<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-semibold mb-1">Utility Management</p>
                <h2 class="font-light text-3xl text-black tracking-tight">
                    Room {{ $room->room_number }} <span class="text-gray-300 mx-2">/</span> <span
                        class="text-gray-500">Electric History</span>
                </h2>
            </div>
            <a href="{{ route('electric-bills.index') }}"
                class="inline-flex items-center px-4 py-2 border border-black text-xs uppercase tracking-widest font-bold text-black bg-white hover:bg-black hover:text-white transition-all duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-gray-100">
                @if ($bills->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b-2 border-black">
                                    <th
                                        class="px-6 py-4 text-left text-[10px] uppercase tracking-[0.3em] font-black text-black">
                                        Billing Month</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] uppercase tracking-[0.3em] font-black text-black">
                                        Total Bill</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] uppercase tracking-[0.3em] font-black text-black">
                                        Tenant Stay & Pro-rating Breakdown</th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] uppercase tracking-[0.3em] font-black text-black">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-4 divide-gray-100">
                                @foreach ($bills as $bill)
                                    @php
                                        $monthStart = \Carbon\Carbon::parse($bill->billing_month)->startOfMonth();
                                        $monthEnd = \Carbon\Carbon::parse($bill->billing_month)->endOfMonth();

                                        $totalRent = 0;
                                        $totalElectricCollected = 0;
                                        $totalDebtCollected = 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-8 whitespace-nowrap v-top">
                                            <span class="text-sm font-bold text-black uppercase tracking-tighter">
                                                {{ $bill->billing_month->format('F Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-8 whitespace-nowrap v-top">
                                            <span class="text-sm font-light text-gray-900">
                                                ₱{{ number_format($bill->total_amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-8 v-top">
                                            <div class="space-y-6">
                                                @foreach ($bill->leasePayments as $payment)
                                                    @php
                                                        $leaseStart = \Carbon\Carbon::parse(
                                                            $payment->lease->start_date,
                                                        )->startOfDay();
                                                        $leaseEnd = \Carbon\Carbon::parse(
                                                            $payment->lease->end_date,
                                                        )->startOfDay();
                                                        $stayStart = $leaseStart->max($monthStart);
                                                        $stayEnd = $leaseEnd->min($monthEnd);
                                                        $days = (int) $stayStart->diffInDays($stayEnd) + 1;

                                                        $isMovingOutThisMonth = $leaseEnd->between(
                                                            $monthStart,
                                                            $monthEnd,
                                                        );
                                                        $isMovingInThisMonth = $leaseStart->between(
                                                            $monthStart,
                                                            $monthEnd,
                                                        );

                                                        $totalRent += $payment->amount;
                                                        $totalElectricCollected += $payment->electric_bill_amount;
                                                        $totalDebtCollected += $payment->carried_over_debt;

                                                        // Logic for Debt Consumption Date Range
                                                        $prevMonth = $bill->billing_month->copy()->subMonth();
                                                        $debtRange =
                                                            $leaseStart
                                                                ->max($prevMonth->copy()->startOfMonth())
                                                                ->format('M d') .
                                                            ' – ' .
                                                            $leaseEnd
                                                                ->min($prevMonth->copy()->endOfMonth())
                                                                ->format('M d, Y');
                                                    @endphp
                                                    <div
                                                        class="flex justify-between items-start border-l-2 {{ $isMovingOutThisMonth ? 'border-red-500 bg-red-50/30' : 'border-black' }} pl-4 py-2 pr-2 transition-colors">
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <p
                                                                    class="text-xs font-bold text-black uppercase tracking-tight">
                                                                    {{ $payment->lease->tenant->full_name }}</p>
                                                                @if ($isMovingOutThisMonth)
                                                                    <span
                                                                        class="text-[8px] bg-red-600 text-white px-1.5 py-0.5 font-black uppercase tracking-widest">Move-out</span>
                                                                @elseif($isMovingInThisMonth)
                                                                    <span
                                                                        class="text-[8px] bg-blue-600 text-white px-1.5 py-0.5 font-black uppercase tracking-widest">New
                                                                        Tenant</span>
                                                                @endif
                                                            </div>

                                                            <div class="flex items-center group relative mt-1">
                                                                <p
                                                                    class="text-[10px] {{ $isMovingOutThisMonth ? 'text-red-700 font-medium' : 'text-gray-400' }}">
                                                                    <i class="fa-regular fa-calendar-check mr-1"></i>
                                                                    Stayed: {{ $stayStart->format('M d, Y') }} –
                                                                    {{ $stayEnd->format('M d, Y') }}
                                                                    <span class="mx-1 text-gray-200">|</span>
                                                                    <span
                                                                        class="text-black font-semibold">{{ $days }}
                                                                        Day(s)</span>
                                                                </p>
                                                            </div>

                                                            @if ($isMovingOutThisMonth)
                                                                <p
                                                                    class="text-[9px] text-red-500 font-bold uppercase mt-1">
                                                                    Final Lease Date: {{ $leaseEnd->format('M d, Y') }}
                                                                </p>
                                                            @endif
                                                            <p class="text-[10px] mt-1 italic text-gray-500">Rent Due:
                                                                {{ $payment->due_date->format('M d, Y') }}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            @if ($payment->electric_bill_amount > 0)
                                                                <p class="text-xs font-bold text-black tracking-tight">
                                                                    Share:
                                                                    ₱{{ number_format($payment->electric_bill_amount, 2) }}
                                                                </p>

                                                                @if ($payment->carried_over_debt > 0)
                                                                    <div class="mt-1">
                                                                        <p
                                                                            class="text-[9px] text-white bg-red-500 px-1 py-0.5 inline-block font-black uppercase">
                                                                            + DEBT:
                                                                            ₱{{ number_format($payment->carried_over_debt, 2) }}
                                                                        </p>
                                                                        <p
                                                                            class="text-[8px] text-red-600 font-bold mt-0.5 uppercase tracking-tighter leading-none">
                                                                            For: {{ $debtRange }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <span
                                                                    class="text-[9px] px-2 py-1 bg-gray-100 text-gray-400 font-bold uppercase tracking-tighter italic">First
                                                                    Month (Deferred)</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div
                                                class="mt-8 pt-4 border-t border-dashed border-gray-200 grid grid-cols-3 gap-4 bg-gray-50/50 p-4">
                                                <div>
                                                    <p
                                                        class="text-[9px] uppercase tracking-widest text-gray-400 font-bold">
                                                        Total Rent</p>
                                                    <p class="text-xs font-bold text-black">
                                                        ₱{{ number_format($totalRent, 2) }}</p>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-[9px] uppercase tracking-widest text-gray-400 font-bold">
                                                        Total Electric</p>
                                                    <p class="text-xs font-bold text-black">
                                                        ₱{{ number_format($totalElectricCollected + $totalDebtCollected, 2) }}
                                                    </p>
                                                </div>
                                                <div class="text-right border-l border-gray-200 pl-4">
                                                    <p
                                                        class="text-[9px] uppercase tracking-widest text-gray-400 font-bold">
                                                        Total Revenue</p>
                                                    <p class="text-sm font-black text-black tracking-tighter">
                                                        ₱{{ number_format($totalRent + $totalElectricCollected + $totalDebtCollected, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-8 whitespace-nowrap text-right v-top">
                                            <button type="button"
                                                onclick="openEditBillModal({{ $bill->id }}, {{ $room->id }}, '{{ $bill->billing_month->format('Y-m') }}', {{ $bill->total_amount }})"
                                                class="text-black hover:underline underline-offset-4 decoration-1 font-bold text-xs uppercase tracking-tighter">Edit
                                                Record</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

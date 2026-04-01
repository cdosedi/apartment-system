<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div class="bg-black -mx-4 -mt-6 px-8 py-10 border-b border-gray-800">
            <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-6">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-gray-500 mb-2">Accounting /
                        {{ strtoupper($viewType) }}LY LEDGER</p>
                    <h2 class="text-4xl font-extralight text-white tracking-tighter">Income Report</h2>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div class="flex bg-zinc-900 p-1 rounded-lg border border-zinc-800">
                        <a href="{{ route('reports.income', ['view_type' => 'month']) }}"
                            class="px-4 py-1.5 text-[9px] font-bold uppercase tracking-widest rounded-md transition-all {{ $viewType === 'month' ? 'bg-white text-black' : 'text-zinc-500 hover:text-white' }}">Month</a>
                        <a href="{{ route('reports.income', ['view_type' => 'year']) }}"
                            class="px-4 py-1.5 text-[9px] font-bold uppercase tracking-widest rounded-md transition-all {{ $viewType === 'year' ? 'bg-white text-black' : 'text-zinc-500 hover:text-white' }}">Year</a>
                    </div>

                    <div
                        class="flex items-center bg-zinc-900 border border-zinc-800 rounded-lg overflow-hidden h-11 shadow-2xl">
                        <a href="{{ route('reports.income', ['view_type' => $viewType, 'date' => $prevDate]) }}"
                            class="px-4 flex items-center h-full border-r border-zinc-800 hover:bg-zinc-800 text-zinc-400">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                        <div class="px-6 flex items-center h-full min-w-[140px] justify-center text-center">
                            <span
                                class="text-white text-[10px] font-black uppercase tracking-[0.2em] whitespace-nowrap">
                                {{ $displayDate }}
                            </span>
                        </div>
                        <a href="{{ route('reports.income', ['view_type' => $viewType, 'date' => $nextDate]) }}"
                            class="px-4 flex items-center h-full border-l border-zinc-800 hover:bg-zinc-800 text-zinc-400">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </div>

                    <form action="{{ route('reports.income') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="view_type" value="{{ $viewType }}">
                        @if ($viewType === 'month')
                            <input type="month" name="date" value="{{ $dateInput }}"
                                onchange="this.form.submit()"
                                class="bg-zinc-900 border-zinc-800 text-white text-[10px] font-bold uppercase rounded-lg h-11 focus:ring-white">
                        @else
                            <select name="date" onchange="this.form.submit()"
                                class="bg-zinc-900 border-zinc-800 text-white text-[10px] font-bold uppercase rounded-lg h-11 focus:ring-white pr-10">
                                @for ($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $dateInput == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        @endif
                    </form>

                    <a href="{{ route('reports.income.download', ['view_type' => $viewType, 'date' => $dateInput]) }}"
                        class="bg-white text-black px-6 h-11 flex items-center text-[10px] font-bold uppercase tracking-widest hover:bg-gray-200 shadow-lg">
                        <i class="fa-solid fa-file-excel mr-2"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-12">
                <div class="p-6 border border-zinc-100 bg-zinc-50">
                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Rent Collected</p>
                    <p class="text-2xl font-light text-black">₱{{ number_format($totalRent, 2) }}</p>
                </div>

                <div class="p-6 border border-zinc-100 bg-zinc-50">
                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Utility Recovery</p>
                    <p class="text-2xl font-light text-black">₱{{ number_format($totalElectricCollected, 2) }}</p>
                </div>

                <div class="p-6 border border-red-100 bg-red-50/30">
                    <p class="text-[9px] font-bold text-red-400 uppercase tracking-widest mb-1">Utility Expenses</p>
                    <p class="text-2xl font-light text-red-600">₱{{ number_format($actualUtilityExpense, 2) }}</p>
                </div>

                <div class="p-6 border border-amber-100 bg-amber-50/30">
                    <p class="text-[9px] font-bold text-amber-500 uppercase tracking-widest mb-1">Total Receivables</p>
                    <p class="text-2xl font-light text-amber-700">₱{{ number_format($totalReceivables, 2) }}</p>
                </div>

                <div class="p-6 bg-black text-white shadow-xl">
                    <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Net Profit</p>
                    <p class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        ₱{{ number_format($totalProfit, 2) }}
                    </p>
                </div>
            </div>

            <div class="space-y-12">
                @forelse ($groupedPayments as $sectionName => $records)
                    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                        <div
                            class="px-8 py-5 bg-zinc-50 border-b border-zinc-200 flex flex-col md:flex-row justify-between items-center gap-4">
                            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-800">
                                <i class="fa-solid fa-calendar-check mr-2 text-zinc-400"></i> {{ $sectionName }}
                            </h3>
                            <div class="bg-white px-4 py-2 rounded border border-zinc-200">
                                <span class="text-[10px] text-zinc-400 uppercase font-bold mr-3 tracking-widest">Section
                                    Total:</span>
                                <span class="text-sm font-bold text-emerald-600">
                                    ₱{{ number_format($records->sum('amount') + $records->sum('electric_bill_amount') + $records->sum('carried_over_debt'), 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-100">
                                <thead class="bg-white">
                                    <tr>
                                        <th
                                            class="px-8 py-4 text-left text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                            Date Settled</th>
                                        <th
                                            class="px-8 py-4 text-left text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                            Tenant</th>
                                        <th
                                            class="px-8 py-4 text-left text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                            Room #</th>
                                        <th
                                            class="px-8 py-4 text-right text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                            Rent</th>
                                        <th
                                            class="px-8 py-4 text-right text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                            Electric</th>
                                        <th
                                            class="px-8 py-4 text-right text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                            Total Paid</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-50">
                                    @foreach ($records as $payment)
                                        <tr class="hover:bg-zinc-50/50 transition-all group">
                                            <td class="px-8 py-5 text-[10px] font-black text-zinc-900 uppercase">
                                                {{ $payment->paid_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-8 py-5 text-sm font-light text-black">
                                                {{ $payment->lease->tenant->full_name }}
                                            </td>
                                            <td class="px-8 py-5 text-sm font-light text-zinc-400">
                                                Unit {{ $payment->lease->room->room_number }}
                                            </td>
                                            <td class="px-8 py-5 text-right text-sm font-light text-zinc-600">
                                                ₱{{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td class="px-8 py-5 text-right text-sm font-light text-zinc-600">
                                                ₱{{ number_format($payment->electric_bill_amount + $payment->carried_over_debt, 2) }}
                                            </td>
                                            <td class="px-8 py-5 text-right text-sm font-bold text-black bg-zinc-50/30">
                                                ₱{{ number_format($payment->amount + $payment->electric_bill_amount + $payment->carried_over_debt, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="py-32 text-center border-2 border-dashed border-zinc-100 rounded-3xl">
                        <i class="fa-solid fa-folder-open text-5xl text-zinc-100 mb-6 block"></i>
                        <p class="text-[10px] font-bold text-zinc-300 uppercase tracking-[0.5em]">No records for
                            {{ $displayDate }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

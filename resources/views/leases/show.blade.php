<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div class="bg-black -mx-4 -mt-6 px-8 py-16 border-b border-white/10">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-end justify-between">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <span class="h-[1px] w-12 bg-white"></span>
                        <p class="text-[10px] font-bold uppercase tracking-[0.5em] text-zinc-500">Legal Documents</p>
                    </div>
                    <h2 class="text-6xl font-extralight text-white tracking-tighter leading-none">
                        Agreement <span class="text-white">- Room</span> {{ $lease->room->room_number }}
                    </h2>
                    <div class="flex items-center space-x-6 pt-2">
                        <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest">Ref:
                            #LSE-{{ $lease->id }}</span>
                        <span class="h-3 w-[1px] bg-zinc-800"></span>
                        <span class="text-[11px] font-bold text-white uppercase tracking-widest">{{ $lease->status }}
                            Lease</span>
                    </div>
                </div>

                <div class="mt-10 md:mt-0">
                    <a href="{{ route('tenants.show', $lease->tenant) }}"
                        class="group inline-flex items-center text-[10px] font-bold uppercase tracking-[0.3em] text-white transition-all">
                        <span class="border-b border-zinc-700 pb-1 group-hover:border-white transition-all">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Close File
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-20">
                <div class="lg:col-span-4 space-y-8">
                    <div>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mb-4">Resident</p>
                        <h3 class="text-2xl font-light text-black tracking-tight">{{ $lease->tenant->full_name }}</h3>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mb-4">Financials</p>
                        <p class="text-2xl font-light text-black tracking-tight">
                            ₱{{ number_format($lease->monthly_rent, 2) }} <span
                                class="text-xs text-zinc-400 uppercase tracking-widest ml-1">/ month</span></p>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('lease-agreement.download', $lease) }}"
                            class="inline-flex items-center px-6 py-3 bg-black text-white text-[10px] font-bold uppercase tracking-widest hover:bg-zinc-800 transition-all shadow-xl shadow-zinc-200">
                            <i class="fa-solid fa-file-pdf mr-2"></i> Download Contract
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="border-l border-zinc-100 pl-12 py-2">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mb-6">Execution Period
                        </p>
                        <div class="flex items-center space-x-12">
                            <div>
                                <p class="text-[9px] font-bold text-zinc-400 uppercase mb-1">Commencement</p>
                                <p class="text-lg font-light text-black">{{ $lease->start_date->format('F d, Y') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-right-long text-zinc-200"></i>
                            <div>
                                <p class="text-[9px] font-bold text-zinc-400 uppercase mb-1">Termination</p>
                                <p class="text-lg font-light text-black">{{ $lease->end_date->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-black">Monthly Settlement Ledger
                    </h3>
                    <p class="text-[10px] text-zinc-400 font-medium italic">All values in PHP (₱)</p>
                </div>

                @if ($lease->payments->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th
                                        class="py-6 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Period</th>
                                    <th
                                        class="py-6 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Amount Due</th>
                                    <th
                                        class="py-6 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="py-6 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Settled Date</th>
                                    <th
                                        class="py-6 text-right text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Archive</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50 border-t border-zinc-100">
                                @foreach ($lease->payments as $payment)
                                    <tr class="group hover:bg-zinc-50/50 transition-all">
                                        <td class="py-8 whitespace-nowrap">
                                            <p class="text-xs font-bold text-black uppercase tracking-tighter">
                                                {{ $payment->due_date->format('M d, Y') }}</p>
                                        </td>
                                        <td class="py-8 whitespace-nowrap">
                                            <p class="text-sm font-light text-zinc-600">
                                                ₱{{ number_format($payment->amount, 2) }}</p>
                                        </td>
                                        <td class="py-8 whitespace-nowrap">
                                            <span
                                                class="inline-block w-2 h-2 rounded-full mr-2 {{ $payment->status === 'paid' ? 'bg-black' : 'bg-red-500' }}"></span>
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-widest {{ $payment->status === 'paid' ? 'text-black' : 'text-red-500' }}">
                                                {{ $payment->status }}
                                            </span>
                                        </td>
                                        <td
                                            class="py-8 whitespace-nowrap text-xs font-medium text-zinc-400 uppercase tracking-tighter">
                                            {{ $payment->status === 'paid' && $payment->paid_at ? $payment->paid_at->format('M d, Y') : '—' }}
                                        </td>
                                        <td class="py-8 whitespace-nowrap text-right">
                                            <div class="flex justify-end items-center space-x-6">
                                                <a href="{{ route('payments.invoice', $payment) }}" target="_blank"
                                                    class="text-[10px] font-bold text-zinc-400 hover:text-black uppercase tracking-widest transition-colors flex items-center">
                                                    <i class="fa-solid fa-file-invoice mr-2 text-[12px]"></i> Invoice
                                                </a>

                                                @if ($payment->status === 'paid')
                                                    <a href="{{ route('payments.receipt', $payment) }}" target="_blank"
                                                        class="text-[10px] font-bold text-black border-b border-black pb-0.5 hover:text-zinc-500 hover:border-zinc-300 uppercase tracking-widest transition-all flex items-center">
                                                        <i class="fa-solid fa-receipt mr-2 text-[12px]"></i> Receipt
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-20 border border-dashed border-zinc-200">
                        <p class="text-[10px] font-bold text-zinc-300 uppercase tracking-[0.5em]">No entries generated
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

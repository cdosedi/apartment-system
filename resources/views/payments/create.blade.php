<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between border-b border-black pb-6">
            <div>
                <p class="text-[10px] uppercase tracking-[0.3em] text-gray-400 font-bold mb-1">Financial Processing</p>
                <h2 class="font-black text-3xl text-black tracking-tighter uppercase leading-none">
                    {{ __('Record Payment') }}
                </h2>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-mono text-gray-400 uppercase">Ref:
                    INV-{{ $payment->id }}{{ now()->format('ymd') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-16 bg-[#f9f9f9] min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-8 sm:p-12 shadow-[0_40px_80px_-15px_rgba(0,0,0,0.1)] border border-gray-50">
                <div class="p-0">
                    <div class="mb-12">
                        <h3 class="text-xl font-light text-black tracking-tight uppercase">
                            {{ $payment->lease->tenant->full_name }}
                        </h3>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold mt-1">
                            Room {{ $payment->lease->room->room_number }} <span class="mx-2 text-gray-200">|</span> Due:
                            {{ $payment->due_date->format('M d, Y') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('payments.store', $payment) }}">
                        @csrf

                        <div class="border-t-[3px] border-black pt-6 mb-12">
                            <h4 class="text-[10px] font-black text-black mb-8 uppercase tracking-[0.4em]">Statement of
                                Account</h4>

                            <div class="space-y-6">
                                <div class="flex justify-between items-start">
                                    <div class="max-w-[70%]">
                                        <span class="text-xs font-black uppercase tracking-widest text-black">Base
                                            Accommodation</span>
                                        <p class="text-[10px] text-gray-400 uppercase mt-1 leading-relaxed">
                                            Monthly rental for Unit {{ $payment->lease->room->room_number }}
                                        </p>
                                    </div>
                                    <span
                                        class="text-sm font-bold text-black font-mono">₱{{ number_format($payment->amount, 2) }}</span>
                                </div>

                                @if ($payment->electric_bill_amount && $payment->electric_bill_id)
                                    <div
                                        class="flex justify-between items-start p-4 bg-yellow-50 border-l-2 border-yellow-400">
                                        <div class="max-w-[70%]">
                                            <span
                                                class="text-xs font-black uppercase tracking-widest text-amber-900 flex items-center">
                                                Utility Share — {{ $payment->due_date->format('M Y') }}
                                            </span>
                                            <p
                                                class="text-[9px] text-amber-700 uppercase mt-1 font-bold tracking-tight">
                                                Distributed from Total Bill:
                                                ₱{{ number_format($payment->electricBill->total_amount, 2) }}
                                            </p>
                                        </div>
                                        <span class="text-sm font-bold text-amber-900 font-mono">+
                                            ₱{{ number_format($payment->electric_bill_amount, 2) }}</span>
                                    </div>
                                @endif

                                @if ($payment->carried_over_debt > 0)
                                    <div
                                        class="flex justify-between items-start p-4 bg-red-50 border-l-2 border-red-500">
                                        <div class="max-w-[70%]">
                                            <span
                                                class="text-xs font-black uppercase tracking-widest text-red-900">Previous
                                                Arrears</span>
                                            <p
                                                class="text-[9px] text-red-700 uppercase mt-1 font-bold tracking-tight italic">
                                                Deferred utility debt from previous months
                                            </p>
                                        </div>
                                        <span class="text-sm font-bold text-red-900 font-mono">+
                                            ₱{{ number_format($payment->carried_over_debt, 2) }}</span>
                                    </div>
                                @endif

                                @php
                                    $grandTotal =
                                        $payment->amount +
                                        ($payment->electric_bill_amount ?? 0) +
                                        ($payment->carried_over_debt ?? 0);
                                @endphp

                                <div class="pt-8 mt-8 border-t border-gray-100 flex justify-between items-end">
                                    <span class="text-[10px] font-black text-black uppercase tracking-[0.5em]">Grand
                                        Total</span>
                                    <span class="text-4xl font-black text-black tracking-tighter">
                                        ₱{{ number_format($grandTotal, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-8 mb-12">
                            <div>
                                <label for="payment_method"
                                    class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">
                                    Settlement Method
                                </label>
                                <select name="payment_method" id="payment_method"
                                    class="w-full border-0 border-b border-gray-200 bg-transparent px-0 py-3 text-sm font-bold uppercase tracking-widest focus:ring-0 focus:border-black transition-colors cursor-pointer"
                                    required>
                                    <option value="cash">Physical Cash</option>
                                    <option value="e-cash">Electronic / Digital Wallet</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-12 p-6 border border-gray-100 italic">
                            <p class="text-[10px] text-gray-400 leading-relaxed uppercase tracking-tight">
                                <span class="font-black text-black not-italic mr-1">Note:</span>
                                This transaction covers rent and utilities for the period of
                                {{ $payment->due_date->format('F Y') }}.
                                Authorization generates a legal receipt and clears pending debt pool.
                            </p>
                        </div>

                        <div class="flex flex-col space-y-4">
                            <button type="submit"
                                class="w-full py-5 bg-black text-white text-[10px] font-black uppercase tracking-[0.3em] hover:bg-gray-800 transition-all shadow-2xl shadow-black/20 active:translate-y-1">
                                Confirm & Authorize Payment
                            </button>

                            <a href="{{ route('tenants.show', $payment->lease->tenant) }}#payments"
                                class="w-full py-4 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest hover:text-black transition-colors">
                                ← Return to Ledger
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center mt-10 text-[9px] text-gray-300 uppercase tracking-[0.4em]">Audit Log Initialized</p>
        </div>
    </div>
</x-app-layout>

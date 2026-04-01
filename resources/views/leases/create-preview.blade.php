<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-900 leading-tight tracking-tight">
            {{ __('Finalizing Agreement') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen mb-20">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-10 flex items-center justify-between px-4">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Step 2 of 2</h3>
                    <p class="text-lg font-bold text-slate-900">Contract Verification</p>
                </div>
                <div class="h-1 bg-slate-100 w-64 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-600 w-full"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-8 border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900">Lease Agreement Preview</h3>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Please review the formal document carefully before
                        signing.</p>
                </div>

                <div class="p-8">
                    <div
                        class="relative border border-slate-200 rounded-2xl bg-slate-50/50 p-4 sm:p-8 mb-8 shadow-inner">
                        <div
                            class="max-h-[550px] overflow-y-auto bg-white rounded-xl shadow-sm border border-slate-100 p-8 sm:p-12 document-scroll">

                            <div class="text-center mb-12 border-b border-slate-100 pb-8">
                                <div class="text-[10px] font-black uppercase tracking-[0.4em] text-indigo-600 mb-2">
                                    Contract of Lease</div>
                                <h1 class="text-3xl font-serif font-bold text-slate-900 mb-2">LEASE AGREEMENT</h1>
                                <p class="text-slate-500 font-medium italic">Casa Oro Residences</p>
                            </div>

                            <div class="space-y-8 text-slate-700 leading-relaxed font-serif text-base">
                                <p>This Lease Agreement ("Agreement") is made and entered into on
                                    <span
                                        class="border-b border-slate-200 px-1 font-bold text-slate-900">{{ now()->format('F d, Y') }}</span>,
                                    by and between:
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-4">
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Landlord</p>
                                        <p class="font-bold text-slate-900 tracking-tight">CASA ORO RESIDENCES</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Tenant</p>
                                        <p class="font-bold text-slate-900 uppercase tracking-widest">
                                            {{ Str::upper($mockLease->tenant->full_name) }}
                                        </p>
                                    </div>
                                </div>

                                <p><strong>Property:</strong> The Landlord hereby leases to the Tenant the premises
                                    identified as
                                    <span
                                        class="font-bold text-slate-900 underline decoration-slate-200 underline-offset-4">UNIT
                                        {{ $mockLease->room->room_number }}</span>, located at Casa Oro.
                                </p>

                                <p><strong>Lease Term:</strong> The duration of this agreement shall be for
                                    <span
                                        class="font-bold text-slate-900 uppercase">{{ $mockLease->duration_display }}</span>,
                                    beginning on
                                    <span
                                        class="font-bold text-slate-900">{{ $mockLease->start_date->format('F d, Y') }}</span>
                                    and concluding on
                                    <span
                                        class="font-bold text-slate-900">{{ $mockLease->end_date->format('F d, Y') }}</span>.
                                </p>

                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 my-8">
                                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3">
                                        Financial Obligation</p>
                                    <p class="text-lg">The Monthly Rent for the premises shall be
                                        <span
                                            class="font-bold text-indigo-700">₱{{ number_format($mockLease->monthly_rent, 2) }}</span>,
                                        payable on or before the 5th day of each calendar month.
                                    </p>
                                </div>

                                <div
                                    class="bg-slate-900 rounded-2xl p-8 text-white text-sm italic leading-relaxed shadow-xl border-l-8 border-indigo-600">
                                    <span
                                        class="block text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-3 not-italic">Binding
                                        Declaration</span>
                                    "I, <strong
                                        class="uppercase tracking-widest text-indigo-200">{{ Str::upper($mockLease->tenant->full_name) }}</strong>,
                                    hereby confirm my agreement to pay the stipulated monthly rent for <strong>UNIT
                                        {{ $mockLease->room->room_number }}</strong>. I acknowledge that I have read and
                                    understood all terms and conditions provided in this contract."
                                </div>

                                <p class="text-xs text-slate-400 font-sans italic">This document is electronically
                                    generated and holds legal validity under the laws of the Republic of the
                                    Philippines.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-16 mt-20 pt-12 border-t border-slate-100">
                                <div class="text-center">
                                    <div class="h-16 flex items-end justify-center">
                                        <span class="font-serif italic text-slate-300">/s/ Authorized Official</span>
                                    </div>
                                    <div class="h-px bg-slate-900 w-full mb-3"></div>
                                    <p class="text-[10px] font-black text-slate-900 uppercase tracking-[0.2em]">Casa Oro
                                        Management</p>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="h-16 flex items-end justify-center text-indigo-600 font-serif italic text-xl">
                                        {{ Str::upper($mockLease->tenant->full_name) }}
                                    </div>
                                    <div class="h-px bg-slate-400 w-full mb-3"></div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tenant
                                        Signature</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('leases.store', $tenant) }}">
                        @csrf
                        {{-- Data from controller (validated and casted) --}}
                        @foreach ($data as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div
                            class="bg-white rounded-2xl p-6 border-2 border-slate-100 transition-all hover:border-indigo-100 group">
                            <div class="flex items-start space-x-4">
                                <div class="flex items-center h-6 mt-1">
                                    <input type="checkbox" name="agreement_accepted" id="agreement_accepted"
                                        value="1" required
                                        class="w-6 h-6 text-slate-900 border-slate-300 rounded-lg focus:ring-indigo-500 focus:ring-offset-0 transition-all cursor-pointer">
                                </div>
                                <div class="text-sm">
                                    <label for="agreement_accepted"
                                        class="font-black text-slate-900 cursor-pointer uppercase tracking-tight">
                                        Confirmation of Renewal
                                    </label>
                                    <p class="text-gray-500 mt-1 font-medium">
                                        By checking this box, you certify that the tenant <span
                                            class="uppercase font-bold text-slate-700">{{ $mockLease->tenant->full_name }}</span>
                                        has verbally or physically agreed to these renewed terms.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-between">
                            <button type="button" onclick="window.history.back()"
                                class="flex items-center space-x-2 text-sm font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Adjust Details</span>
                            </button>

                            <button type="submit"
                                class="group relative flex items-center justify-center px-10 py-5 bg-slate-900 text-white rounded-2xl overflow-hidden transition-all hover:bg-emerald-600 active:scale-95 shadow-2xl shadow-slate-200">
                                <span class="relative z-10 font-black tracking-widest uppercase text-xs">Confirm &
                                    Activate Lease</span>
                                <div
                                    class="absolute inset-0 bg-emerald-500 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out">
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .document-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .document-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .document-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .document-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-app-layout>

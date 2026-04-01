<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-slate-900 tracking-tighter">
                {{ __('Final Review') }}
            </h2>
            <span
                class="px-4 py-1.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg shadow-slate-200">
                Step 04 / 04
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-12">
                <x-modern-progress :currentStep="4" :progress="100" />
            </div>

            <div class="space-y-10">
                <div class="text-center">
                    <h3 class="text-2xl font-light text-slate-900 tracking-tight">Review & Confirm</h3>
                    <p class="text-gray-500 mt-1 font-medium">Please perform a final verification of the lease registry.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">

                    <div class="lg:col-span-2 space-y-8">

                        <section class="group">
                            <div class="flex items-center space-x-3 mb-5">
                                <div
                                    class="p-2.5 bg-slate-900 rounded-xl shadow-lg shadow-slate-100 transition-transform group-hover:scale-110">
                                    <i class="fa-solid fa-user text-white text-xs"></i>
                                </div>
                                <h4 class="font-black text-slate-900 uppercase text-[10px] tracking-[0.3em]">Tenant
                                    Profile</h4>
                            </div>

                            <div
                                class="bg-white border-2 border-slate-50 rounded-3xl p-8 grid grid-cols-2 gap-y-6 gap-x-8 shadow-sm">
                                <div class="col-span-2">
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Full
                                        Legal Name</p>
                                    <p class="text-lg font-black text-slate-900 uppercase tracking-wider">
                                        {{ Str::upper($tenantData['full_name']) }}</p>
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">Email
                                        Address</p>
                                    <p class="text-sm font-bold text-slate-700">
                                        {{ $tenantData['email'] ?? 'NOT PROVIDED' }}</p>
                                </div>
                                <div class="col-span-2 md:col-span-1">
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">
                                        Primary Contact</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $tenantData['contact_number'] }}</p>
                                </div>
                                <div class="col-span-2 border-t border-slate-50 pt-6">
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-1">
                                        Permanent Residence</p>
                                    <p
                                        class="text-sm font-medium text-slate-600 leading-relaxed uppercase tracking-tight">
                                        {{ $tenantData['address'] }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="group">
                            <div class="flex items-center space-x-3 mb-5">
                                <div
                                    class="p-2.5 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100 transition-transform group-hover:scale-110">
                                    <i class="fa-solid fa-calendar-days text-white text-xs"></i>
                                </div>
                                <h4 class="font-black text-slate-900 uppercase text-[10px] tracking-[0.3em]">Agreement
                                    Logistics</h4>
                            </div>

                            <div class="bg-slate-900 rounded-3xl p-8 grid grid-cols-2 md:grid-cols-3 gap-8 shadow-xl">
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-2">
                                        Effective Date</p>
                                    <p class="text-sm font-bold text-white uppercase">
                                        {{ \Carbon\Carbon::parse($leaseData['start_date'])->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-2">
                                        Expiry Date</p>
                                    <p class="text-sm font-bold text-white uppercase">
                                        {{ \Carbon\Carbon::parse($leaseData['end_date'])->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-indigo-400 uppercase font-black tracking-widest mb-2">
                                        Assigned Unit</p>
                                    <p class="text-sm font-black text-indigo-300 uppercase italic">
                                        Room {{ $leaseData['room_number'] }}</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="lg:col-span-1">
                        <div
                            class="sticky top-8 bg-white border-2 border-slate-100 rounded-[2.5rem] p-8 shadow-2xl shadow-slate-100">
                            <h4
                                class="font-black text-slate-900 uppercase text-[10px] tracking-[0.3em] mb-8 text-center underline decoration-indigo-500 underline-offset-8">
                                Financial Summary</h4>

                            <div class="space-y-5 mb-10">
                                <div class="flex justify-between items-end">
                                    <span
                                        class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Rate</span>
                                    <span
                                        class="font-black text-slate-900 text-lg">₱{{ number_format($leaseData['monthly_rent'], 2) }}</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <span
                                        class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Tenure</span>
                                    <span class="font-bold text-slate-700">{{ $leaseData['duration_months'] }}
                                        Months</span>
                                </div>
                                <div class="pt-5 border-t border-slate-100 flex justify-between items-center">
                                    <span
                                        class="text-[10px] font-black uppercase text-indigo-600 tracking-widest">Status</span>
                                    <span
                                        class="text-emerald-600 font-black flex items-center text-[10px] tracking-widest">
                                        <i class="fa-solid fa-circle-check mr-1.5"></i> SIGNED
                                    </span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('tenants.store-with-lease') }}" class="space-y-4">
                                @csrf
                                @foreach ($data as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                <button type="submit"
                                    class="group relative w-full py-5 bg-slate-900 text-white font-black uppercase text-[10px] tracking-[0.2em] rounded-2xl overflow-hidden shadow-xl hover:shadow-indigo-200 transition-all active:scale-95">
                                    <span class="relative z-10">Finalize & Execute</span>
                                    <div
                                        class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    </div>
                                </button>

                                <button type="button" onclick="window.history.back()"
                                    class="w-full py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors">
                                    ← Back to Edit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

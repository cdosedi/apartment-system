<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight">
                    {{ __('Generate Lease Agreement') }}
                </h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">Tenant Placement Module
                </p>
            </div>
            <div class="hidden sm:block">
                <i class="fa-solid fa-file-contract text-slate-200 text-4xl"></i>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 md:p-12">

                    <div class="mb-10 pb-10 border-b border-slate-50 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Lease Configuration</h3>
                            <p class="text-sm text-gray-500 mt-1 font-medium">Setting up terms for <span
                                    class="text-indigo-600 font-bold">{{ $tenant->full_name }}</span></p>
                        </div>
                        <div
                            class="h-12 w-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                            <i class="fa-solid fa-signature"></i>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('leases.preview', $tenant) }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label for="start_date"
                                    class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Lease
                                    Start Date</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ old('start_date', now()->toDateString()) }}"
                                    class="w-full rounded-2xl border-slate-100 bg-slate-50/50 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold py-4 px-5 @error('start_date') border-red-300 @enderror"
                                    required>
                                @error('start_date')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_months"
                                    class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Contract
                                    Duration</label>
                                <select name="duration_months" id="duration_months"
                                    class="w-full rounded-2xl border-slate-100 bg-slate-50/50 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold py-4 px-5"
                                    required>
                                    <option value="">-- Select Duration --</option>
                                    @for ($m = 1; $m <= 60; $m++)
                                        @php
                                            // Inline logic for the dropdown display
                                            $y = floor($m / 12);
                                            $rem = $m % 12;
                                            $display = '';

                                            if ($y > 0) {
                                                $display .= $y . ($y == 1 ? ' Year' : ' Years');
                                            }
                                            if ($rem > 0) {
                                                $display .=
                                                    ($y > 0 ? ' & ' : '') . $rem . ($rem == 1 ? ' Month' : ' Months');
                                            }
                                        @endphp
                                        <option value="{{ $m }}"
                                            {{ old('duration_months') == $m ? 'selected' : '' }}>
                                            {{ $display }}
                                        </option>
                                    @endfor
                                </select>
                                @error('duration_months')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date"
                                    class="block text-xs font-black uppercase tracking-widest text-indigo-400 mb-3 ml-1">Calculated
                                    Termination</label>
                                <div class="relative">
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="w-full rounded-2xl border-indigo-100 bg-indigo-50/30 text-indigo-700 font-black py-4 px-5 cursor-not-allowed"
                                        readonly>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-indigo-300">
                                        <i class="fa-solid fa-calculator text-xs"></i>
                                    </div>
                                </div>
                                <p class="mt-2 text-[10px] font-bold text-indigo-300 uppercase tracking-tighter ml-1">
                                    Automatically set by system</p>
                            </div>

                            <div>
                                <label for="monthly_rent"
                                    class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Monthly
                                    Rent</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 font-black text-sm">
                                        ₱</div>
                                    <input type="number" name="monthly_rent" id="monthly_rent" step="0.01"
                                        min="100" value="{{ old('monthly_rent') }}"
                                        class="w-full rounded-2xl border-slate-100 bg-slate-50/50 pl-10 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-black py-4 px-5 @error('monthly_rent') border-red-300 @enderror"
                                        placeholder="0.00" required>
                                </div>
                                @error('monthly_rent')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="room_id"
                                    class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Unit
                                    Assignment</label>
                                <select name="room_id" id="room_id"
                                    class="w-full rounded-2xl border-slate-100 bg-slate-50/50 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-black py-4 px-5 @error('room_id') border-red-300 @enderror"
                                    required>
                                    <option value="">-- Select an Available Room --</option>
                                    @foreach ($availableRooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            Unit {{ $room->room_number }} — Ready for Occupancy
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-end space-x-4 pt-8 border-t border-slate-50">
                            <a href="{{ route('tenants.show', $tenant) }}"
                                class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 hover:text-slate-600 transition-all">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-10 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-white bg-slate-900 border border-slate-900 rounded-2xl hover:bg-indigo-600 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-100 transition-all active:scale-95">
                                Preview Agreement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startInput = document.getElementById('start_date');
            const durationSelect = document.getElementById('duration_months');
            const endInput = document.getElementById('end_date');

            function updateEndDate() {
                if (!startInput.value || !durationSelect.value) return;

                const startDate = new Date(startInput.value);
                const months = parseInt(durationSelect.value);
                const endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + months);
                endDate.setDate(endDate.getDate() - 1);

                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                endInput.value = `${year}-${month}-${day}`;
            }

            startInput.addEventListener('change', updateEndDate);
            durationSelect.addEventListener('change', updateEndDate);
            updateEndDate();
        });
    </script>
</x-app-layout>

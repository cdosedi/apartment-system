<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-900 leading-tight tracking-tight">
            {{ __('Create Tenant & Lease') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <x-modern-progress :currentStep="2" :progress="50" />
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-8">
                    <div class="mb-10 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Lease Information</h3>
                            <p class="text-sm text-gray-500 mt-1 font-medium">Configure lease details for <span
                                    class="text-indigo-600 font-bold">{{ $tenantData['full_name'] }}</span></p>
                        </div>
                        <div class="hidden sm:block">
                            <i class="fa-solid fa-file-signature text-slate-200 text-4xl"></i>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('tenants.agreement-preview') }}">
                        @csrf

                        @foreach ($tenantData as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date"
                                    class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Lease
                                    Start Date</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ old('start_date', now()->toDateString()) }}"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium py-3"
                                    required>
                                @error('start_date')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_months"
                                    class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Lease
                                    Duration</label>
                                <select name="duration_months" id="duration_months"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium py-3"
                                    required>
                                    <option value="">-- Select Duration --</option>
                                    @for ($m = 1; $m <= 60; $m++)
                                        @php
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
                                    class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2 text-indigo-600">Calculated
                                    Lease End</label>
                                <div class="relative">
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="w-full rounded-xl border-slate-200 bg-indigo-50/30 text-indigo-700 font-bold py-3 cursor-not-allowed"
                                        readonly>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-indigo-300">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </div>
                                </div>
                                <p class="mt-2 text-[10px] font-bold text-indigo-400 uppercase tracking-tighter">System
                                    calculated based on duration</p>
                            </div>

                            <div>
                                <label for="monthly_rent"
                                    class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Monthly
                                    Rent (₱)</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">
                                        ₱</div>
                                    <input type="number" name="monthly_rent" id="monthly_rent" step="0.01"
                                        min="100" value="{{ old('monthly_rent') }}"
                                        class="w-full rounded-xl border-slate-200 bg-slate-50/50 pl-8 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold py-3 placeholder-gray-400"
                                        placeholder="0.00" required>
                                </div>
                                @error('monthly_rent')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="room_id"
                                    class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Room /
                                    Unit Assignment</label>
                                <select name="room_id" id="room_id"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold py-3"
                                    required>
                                    <option value="">-- Select an Available Room --</option>
                                    @foreach ($availableRooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            Unit {{ $room->room_number }} — Available
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-end space-x-4 border-t border-slate-100 pt-8">
                            <button type="button" onclick="window.history.back()"
                                class="px-6 py-3 text-sm font-black uppercase tracking-widest text-slate-400 bg-white border-2 border-slate-100 rounded-xl hover:bg-slate-50 hover:text-slate-600 transition-all active:scale-95">
                                Back
                            </button>
                            <button type="submit"
                                class="px-8 py-3 text-sm font-black uppercase tracking-widest text-white bg-slate-900 border-2 border-slate-900 rounded-xl hover:bg-indigo-600 hover:border-indigo-600 hover:shadow-lg hover:shadow-indigo-200 transition-all active:scale-95">
                                Continue to Review
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

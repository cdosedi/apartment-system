<x-app-layout>
    <x-slot name="header">
        <div
            class="relative overflow-hidden bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500 p-8 -mx-4 sm:-mx-8 shadow-inner">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-amber-900 font-bold mb-1 opacity-80">Utility
                        Dashboard</p>
                    <h2 class="font-black text-3xl text-amber-950 tracking-tighter">
                        {{ __('Electric Bills') }}
                    </h2>
                </div>
                <div
                    class="text-xs font-medium text-amber-900/80 bg-white/20 backdrop-blur-md px-4 py-3 rounded-lg border border-white/30 max-w-sm">
                    <i class="fa-solid fa-bolt-lightning mr-2"></i>
                    Bills automatically merge with lease payments for a single unified receipt.
                </div>
            </div>
            <i class="fa-solid fa-bolt absolute -right-4 -bottom-6 text-9xl text-white/10 rotate-12"></i>
        </div>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="mb-8 border-l-4 border-black bg-gray-50 p-4 text-xs font-bold uppercase tracking-widest text-black">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mb-8 border-l-4 border-red-600 bg-red-50 p-4 text-xs font-medium text-red-700 uppercase tracking-wide">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if ($rooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @foreach ($rooms as $room)
                        @php
                            $currentMonth = now()->format('Y-m-01');
                            $currentBill = $room->electricBills()->where('billing_month', $currentMonth)->first();
                        @endphp

                        <div class="group border-t-2 border-black pt-6 transition-all">
                            <div class="mb-6">
                                <div class="flex justify-between items-end mb-2">
                                    <h3 class="text-2xl font-black tracking-tighter text-black uppercase">
                                        Room {{ $room->room_number }}
                                    </h3>
                                    <span
                                        class="text-[10px] uppercase tracking-widest font-bold {{ $room->available_beds > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $room->available_beds > 0 ? 'Available' : 'Full' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 uppercase tracking-widest">
                                    {{ $room->activeLeases->count() }} Active Tenants <span
                                        class="mx-1 text-gray-300">/</span> {{ $room->bed_capacity }} Capacity
                                </p>
                            </div>

                            @if ($currentBill)
                                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p
                                                class="text-[9px] font-black text-yellow-800 uppercase tracking-widest mb-1">
                                                Current Bill</p>
                                            <p class="text-xl font-black text-black tracking-tight">
                                                ₱{{ number_format($currentBill->total_amount, 2) }}
                                            </p>
                                        </div>
                                        <button type="button"
                                            onclick="openEditBillModal({{ $currentBill->id }}, {{ $room->id }}, '{{ $currentBill->billing_month->format('Y-m') }}', {{ $currentBill->total_amount }})"
                                            class="h-8 w-8 flex items-center justify-center rounded-full border border-black hover:bg-black hover:text-white transition-all">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-8">
                                <div class="flex justify-between items-center border-b border-gray-100 pb-2 mb-4">
                                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-black">New Entry
                                    </h4>
                                    <a href="{{ route('electric-bills.room', $room->id) }}"
                                        class="text-[10px] uppercase font-bold text-gray-400 hover:text-black transition-colors">
                                        View History →
                                    </a>
                                </div>

                                <form method="POST" action="{{ route('electric-bills.store') }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-[9px] uppercase font-bold text-gray-400 mb-1">Month</label>
                                            <input type="month" name="billing_month" required
                                                class="w-full border-0 border-b border-gray-200 p-0 py-2 text-xs focus:ring-0 focus:border-black transition-colors bg-transparent"
                                                value="{{ date('Y-m') }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[9px] uppercase font-bold text-gray-400 mb-1">Amount</label>
                                            <input type="number" name="total_amount" step="0.01" min="1"
                                                required
                                                class="w-full border-0 border-b border-gray-200 p-0 py-2 text-xs focus:ring-0 focus:border-black transition-colors bg-transparent"
                                                placeholder="0.00">
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full py-3 bg-black text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-gray-800 transition-all shadow-lg shadow-gray-200">
                                        Distribute Bill
                                    </button>
                                </form>
                            </div>

                            <div class="space-y-2">
                                @forelse ($room->activeLeases as $lease)
                                    <a href="{{ route('tenants.show', $lease->tenant) }}"
                                        class="flex items-center justify-between group/tenant">
                                        <div class="flex items-center">
                                            <div
                                                class="h-6 w-6 border border-gray-200 flex items-center justify-center text-[10px] font-bold mr-3 group-hover/tenant:border-black">
                                                {{ strtoupper(substr($lease->tenant->full_name, 0, 1)) }}
                                            </div>
                                            <span
                                                class="text-xs text-gray-600 group-hover/tenant:text-black font-medium transition-colors">
                                                {{ $lease->tenant->full_name }}
                                            </span>
                                        </div>
                                        <i
                                            class="fa-solid fa-chevron-right text-[8px] text-gray-300 opacity-0 group-hover/tenant:opacity-100 transition-all"></i>
                                    </a>
                                @empty
                                    <p class="text-[10px] uppercase font-bold text-gray-300 italic">No Active Leases</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12 pt-8 border-t border-gray-100">
                    {{ $rooms->links() }}
                </div>
            @else
                <div class="text-center py-24 border border-dashed border-gray-200">
                    <h3 class="text-xs uppercase tracking-[0.5em] font-black text-gray-400">Inventory Empty</h3>
                    <a href="{{ route('rooms.index') }}"
                        class="mt-4 inline-block text-[10px] uppercase font-bold underline underline-offset-8">
                        Configure Rooms
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div id="editBillModal"
        class="fixed inset-0 bg-black/90 hidden overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
        <div class="relative top-20 mx-auto p-10 w-full max-w-md bg-white">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-xs font-black uppercase tracking-[0.4em]">Adjust Entry</h3>
                <button onclick="closeEditBillModal()" class="text-gray-400 hover:text-black transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="editBillForm" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_bill_id" name="bill_id">
                <input type="hidden" id="edit_room_id" name="room_id">

                <div>
                    <label class="block text-[10px] uppercase font-black text-gray-400 mb-2">Month</label>
                    <input type="month" id="edit_billing_month" name="billing_month" required
                        class="w-full border-0 border-b border-gray-200 p-0 py-3 text-sm font-bold text-gray-400 bg-transparent focus:ring-0"
                        readonly>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-black text-gray-800 mb-2">Total Amount (₱)</label>
                    <input type="number" id="edit_total_amount" name="total_amount" step="0.01" min="1"
                        required
                        class="w-full border-0 border-b border-gray-200 p-0 py-3 text-2xl font-black focus:ring-0 focus:border-black transition-colors">
                </div>

                <div class="flex flex-col gap-3 pt-6">
                    <button type="submit"
                        class="w-full py-4 bg-black text-white text-[10px] font-black uppercase tracking-widest hover:bg-gray-800">
                        Update Record
                    </button>
                    <button type="button" onclick="closeEditBillModal()"
                        class="w-full py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-black">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditBillModal(billId, roomId, billingMonth, totalAmount) {
            document.getElementById('edit_bill_id').value = billId;
            document.getElementById('edit_room_id').value = roomId;
            document.getElementById('edit_billing_month').value = billingMonth;
            document.getElementById('edit_total_amount').value = totalAmount;
            document.getElementById('editBillForm').action = `/electric-bills/${billId}`;
            document.getElementById('editBillModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditBillModal() {
            document.getElementById('editBillModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('editBillModal');
            if (e.target === modal) closeEditBillModal();
        });
    </script>
</x-app-layout>

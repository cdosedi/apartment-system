<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-800 to-violet-900 p-8 -mx-4 sm:-mx-8 shadow-2xl">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-64 w-64 bg-indigo-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span
                            class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black text-indigo-100 uppercase tracking-widest border border-white/10">
                            Admin Portal
                        </span>
                    </div>
                    <h2 class="font-black text-3xl text-white tracking-tighter">
                        {{ __('Tenant Directory') }}
                    </h2>
                    <p class="text-sm font-medium text-indigo-200/80 mt-1 flex items-center">
                        <i class="fa-solid fa-database mr-2 text-xs opacity-50"></i>
                        Managing {{ $tenants->total() }} registered resident profiles
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('tenants.create') }}"
                        class="group inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-50 transition-all shadow-xl shadow-black/20 active:scale-95">
                        <i class="fa-solid fa-user-plus mr-2 group-hover:rotate-12 transition-transform"></i>
                        Create Tenant
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50/30 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 bg-white p-4 rounded-[2rem] shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('tenants.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-grow max-w-md">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search name, email, or phone..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all">
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-gray-800 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-gray-900 transition-all">
                        Filter
                    </button>

                    @if (request('search'))
                        <a href="{{ route('tenants.index') }}"
                            class="px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-all">
                            <i class="fa-solid fa-xmark mr-1"></i> Clear
                        </a>
                    @endif
                </form>
            </div>

            @if (session('error'))
                <div
                    class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-2xl shadow-sm italic text-sm text-rose-700 font-medium">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                    Tenant Info</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                    Assignment</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                    Status</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                    Lease End</th>
                                <th
                                    class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($tenants as $tenant)
                                @php
                                    $lease = $tenant->activeLease;
                                    $roomNum = $lease?->room?->room_number ?? null;
                                    $endDate = $lease ? $lease->end_date->format('M d, Y') : 'No active date';
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-sm shadow-sm">
                                                {{ strtoupper(substr($tenant->full_name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <p
                                                    class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                                    {{ $tenant->full_name }}</p>
                                                <p class="text-[10px] text-gray-400 font-medium tracking-wide">
                                                    {{ $tenant->email ?? 'No email provided' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        @if ($roomNum)
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold rounded-lg uppercase tracking-tight italic">
                                                <i class="fa-solid fa-door-closed mr-1.5 opacity-50"></i> Room
                                                {{ $roomNum }}
                                            </span>
                                        @else
                                            <span
                                                class="text-[10px] font-bold text-gray-300 uppercase italic tracking-widest">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        @if ($tenant->status === 'active')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                Active
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter bg-gray-100 text-gray-400 border border-gray-200">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-8 py-5 whitespace-nowrap text-[11px] font-bold text-gray-500 uppercase tracking-tight">
                                        {{ $endDate }}
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-right text-sm">
                                        <div class="flex justify-end items-center space-x-3">
                                            <a href="{{ route('tenants.show', $tenant) }}"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm shadow-indigo-100"
                                                title="View Profile">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                            </a>

                                            @if ($tenant->status === 'inactive')
                                                <form action="{{ route('tenants.restore', $tenant) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="h-8 w-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm shadow-emerald-100"
                                                        title="Restore">
                                                        <i class="fa-solid fa-rotate-left text-xs"></i>
                                                    </button>
                                                </form>

                                                <button type="button"
                                                    onclick="confirmDelete('{{ $tenant->full_name }}', '{{ route('tenants.delete', $tenant) }}')"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm shadow-rose-100"
                                                    title="Delete Permanently">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                    onclick="{{ $tenant->hasActiveLease() ? "showActiveLeaseModal('$tenant->full_name')" : 'submitDeactivate(this)' }}"
                                                    data-url="{{ route('tenants.destroy', $tenant) }}"
                                                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-rose-50 hover:text-rose-600 transition-all"
                                                    title="Deactivate">
                                                    <i class="fa-solid fa-user-slash text-xs"></i>
                                                </button>
                                                <form id="deactivate-{{ $tenant->id }}"
                                                    action="{{ route('tenants.destroy', $tenant) }}" method="POST"
                                                    class="hidden">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fa-solid fa-users-slash text-4xl text-gray-100 mb-4"></i>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                                {{ request('search') ? "No results for \"" . request('search') . "\"" : 'The directory is currently empty' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8">
                {{ $tenants->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>

    <div id="activeLeaseModal" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
            onclick="closeActiveLeaseModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 text-center animate-fadeIn">
                <div
                    class="h-16 w-16 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-circle-exclamation text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 tracking-tight mb-2">Active Lease Lock</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-8">
                    <span id="modal-tenant-name" class="font-bold text-gray-800"></span> cannot be deactivated because
                    they have an active contract. Please end the lease first.
                </p>
                <button onclick="closeActiveLeaseModal()"
                    class="w-full py-4 bg-gray-900 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">
                    Understood
                </button>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 text-center animate-fadeIn">
                <div
                    class="h-16 w-16 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-trash-can text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 tracking-tight mb-2">Destroy Record?</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-8">
                    Are you sure you want to permanently remove <span id="delete-tenant-name"
                        class="font-bold text-gray-800"></span>? This cannot be reversed.
                </p>
                <form id="delete-form" method="POST" class="flex flex-col gap-3">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full py-4 bg-rose-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-100">
                        Confirm Deletion
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="w-full py-4 bg-gray-100 text-gray-500 rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-all">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showActiveLeaseModal(tenantName) {
            document.getElementById('modal-tenant-name').textContent = tenantName;
            document.getElementById('activeLeaseModal').classList.remove('hidden');
        }

        function closeActiveLeaseModal() {
            document.getElementById('activeLeaseModal').classList.add('hidden');
        }

        function confirmDelete(tenantName, url) {
            document.getElementById('delete-tenant-name').textContent = tenantName;
            document.getElementById('delete-form').action = url;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function submitDeactivate(btn) {
            if (confirm('Are you sure you want to deactivate this tenant?')) {
                const url = btn.getAttribute('data-url');
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</x-app-layout>

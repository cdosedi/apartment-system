<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div class="bg-black -mx-4 -mt-6 px-8 py-12 border-b border-gray-800">
            <div class="flex flex-col md:flex-row md:items-end justify-between">
                <div class="space-y-2">
                    <div class="flex items-center space-x-3">
                        <span class="h-[1px] w-8 bg-white"></span>
                        <h2 class="text-5xl font-extralight text-white tracking-tighter leading-none">
                            Resident Profile
                        </h2>
                    </div>
                    <div class="flex items-center mt-4 space-x-4 text-[11px] text-gray-400 font-light">
                        <span>UID: {{ $tenant->id }}</span>
                        <span class="text-gray-700">/</span>
                        <span>EST. {{ $tenant->created_at->format('Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-8 mt-10 md:mt-0">
                    <a href="{{ route('tenants.edit', $tenant) }}"
                        class="group relative text-[10px] font-bold uppercase tracking-[0.2em] text-white">
                        <span class="relative z-10">Edit Details</span>
                        <span
                            class="absolute bottom-[-6px] left-0 w-0 h-[1px] bg-white transition-all duration-300 group-hover:w-full"></span>
                    </a>

                    @if (!$tenant->activeLease)
                        <a href="{{ route('leases.create', $tenant) }}"
                            class="px-10 py-3 border border-white text-white text-[10px] font-bold uppercase tracking-[0.3em] hover:bg-white hover:text-black transition-all duration-500">
                            New Lease
                        </a>
                    @else
                        <div class="px-6 py-2 border border-gray-800 bg-transparent">
                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-white">
                                Status: <span class="text-gray-500">Active</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-8 md:p-12 bg-yellow-600">
                    <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-8">
                        <div
                            class="h-24 w-24 bg-black text-white rounded-full flex items-center justify-center text-3xl font-bold border-2 border-gray-300">
                            {{ strtoupper(substr($tenant->full_name, 0, 1)) }}
                        </div>
                        <div class="text-center md:text-left">
                            <h1 class="text-3xl font-bold text-white">{{ $tenant->full_name }}</h1>
                            <p class="text-white flex items-center justify-center md:justify-start mt-2">
                                <i class="fa-solid fa-envelope mr-2 text-sm text-white"></i>
                                {{ $tenant->email ?: 'No email provided' }}
                            </p>
                        </div>
                    </div>
                </div>


                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-8 py-6" aria-label="Tabs">
                        <button onclick="switchTab(event, 'info')"
                            class="tab-link px-1 py-3 text-sm font-medium border-b-2 border-transparent transition-all"
                            data-tab="info">
                            <i class="fa-solid fa-user mr-2 text-gray-900"></i>Personal Info
                        </button>
                        <button onclick="switchTab(event, 'leases')"
                            class="tab-link px-1 py-3 text-sm font-medium border-b-2 border-transparent transition-all text-gray-600 hover:text-gray-900 hover:border-gray-300"
                            data-tab="leases">
                            <i class="fa-solid fa-file-contract mr-2 text-gray-900"></i>Lease History
                        </button>
                        <button onclick="switchTab(event, 'payments')"
                            class="tab-link px-1 py-3 text-sm font-medium border-b-2 border-transparent transition-all text-gray-600 hover:text-gray-900 hover:border-gray-300"
                            data-tab="payments">
                            <i class="fa-solid fa-file-invoice-dollar mr-2 text-gray-900"></i>Invoice & Payments
                        </button>
                    </nav>
                </div>

                <div class="p-8">

                    <div id="panel-info" class="tab-content animate-fadeIn">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fa-solid fa-address-book mr-2 text-gray-900"></i>Contact Details
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="bg-gray-100 p-3 rounded-lg mr-4 text-gray-900">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 font-medium">Phone Number</p>
                                            <p class="text-gray-900 font-semibold">{{ $tenant->contact_number }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="bg-gray-100 p-3 rounded-lg mr-4 text-gray-900">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 font-medium">Current Address</p>
                                            <p class="text-gray-900 font-semibold leading-relaxed">
                                                {{ $tenant->address }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fa-solid fa-user-shield mr-2 text-gray-900"></i>Emergency Contact
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Contact Name</p>
                                        <p class="text-gray-900 font-bold text-lg">
                                            {{ $tenant->emergency_contact_name }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Contact Number</p>
                                        <p class="text-gray-900 font-bold underline">
                                            {{ $tenant->emergency_contact_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="panel-leases" class="tab-content hidden animate-fadeIn">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Lease History</h3>

                            <div class="mt-4 md:mt-0">
                                <label for="lease-filter" class="text-sm font-medium text-gray-700 mr-2">Filter by
                                    Lease:</label>
                                <select id="lease-filter"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                                    <option value="all" {{ $selectedFilter === 'all' ? 'selected' : '' }}>All Leases
                                    </option>
                                    @foreach ($leaseFilters as $key => $filter)
                                        <option value="{{ $key }}"
                                            {{ $selectedFilter === $key ? 'selected' : '' }}>
                                            {{ $filter['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="leases-content">
                            @if ($filteredLeases->count())
                                <div class="space-y-4">
                                    @foreach ($filteredLeases as $lease)
                                        <div
                                            class="bg-white border {{ $lease->status === 'active' ? 'border-green-200' : 'border-red-200' }} rounded-lg p-6 hover:shadow-md transition-shadow">
                                            <div
                                                class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                <div class="flex items-center">
                                                    <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                                        <i class="fa-solid fa-door-open text-gray-900"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-lg">Room
                                                            {{ $lease->room->room_number }}</h4>
                                                        <p class="text-sm text-gray-600">
                                                            {{ $lease->start_date->format('M d, Y') }} →
                                                            {{ $lease->end_date->format('M d, Y') }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex-1 mt-4 md:mt-0">
                                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                                        <div>
                                                            <p class="text-xs text-gray-500 uppercase font-medium">
                                                                Monthly Rent</p>
                                                            <p class="text-lg font-bold text-gray-900">
                                                                ₱{{ number_format($lease->monthly_rent, 2) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-500 uppercase font-medium">
                                                                Status</p>
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold rounded-full
                                                                {{ $lease->status === 'active'
                                                                    ? 'bg-green-100 text-green-800'
                                                                    : ($lease->status === 'expired'
                                                                        ? 'bg-red-100 text-red-800'
                                                                        : 'bg-gray-100 text-gray-600') }}">
                                                                {{ ucfirst($lease->status) }}
                                                            </span>
                                                        </div>
                                                        <div class="md:text-right">
                                                            <a href="{{ route('leases.show', $lease) }}"
                                                                class="inline-flex items-center text-gray-900 hover:text-gray-700 font-medium underline">
                                                                View Details <i
                                                                    class="fa-solid fa-arrow-right ml-1"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="inline-block p-4 bg-gray-100 rounded-full mb-4">
                                        <i class="fa-solid fa-file-contract text-gray-400 text-3xl"></i>
                                    </div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-2">
                                        @if ($selectedFilter === 'all')
                                            No lease history found
                                        @else
                                            No lease found for this period
                                        @endif
                                    </h4>
                                    <p class="text-gray-500">
                                        @if ($selectedFilter === 'all')
                                            This tenant has no lease records.
                                        @else
                                            No lease matches the selected filter.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div id="panel-payments" class="tab-content hidden animate-fadeIn">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Invoice & Payments</h3>

                            <div class="mt-4 md:mt-0">
                                <label for="payment-lease-filter"
                                    class="text-sm font-medium text-gray-700 mr-2">Filter by Lease:</label>
                                <select id="payment-lease-filter"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                                    <option value="all" {{ $selectedFilter === 'all' ? 'selected' : '' }}>All
                                        Leases</option>
                                    @foreach ($leaseFilters as $key => $filter)
                                        <option value="{{ $key }}"
                                            {{ $selectedFilter === $key ? 'selected' : '' }}>
                                            {{ $filter['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="payments-content">
                            @include('partials.payments-table-lease', [
                                'payments' => $payments,
                                'leases' => $leases,
                                'filter' => $selectedFilter,
                                'leaseFilters' => $leaseFilters,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="payAllLeaseModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-[9999]">
        <div class="relative top-20 mx-auto p-5 border border-gray-900 w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-bold text-gray-900 mt-2">Confirm Full Payment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-600">
                        You are about to pay <strong id="modal-unpaid-count"></strong> remaining months totaling
                        <strong>₱<span id="modal-total-amount"></span></strong>.
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        This will generate <span id="modal-unpaid-count-2"></span> receipts.
                    </p>
                </div>
                <form id="payAllLeaseForm" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="lease_id" id="modal-lease-id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select name="payment_method" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                            <option value="cash">Cash</option>
                            <option value="e-cash">E-Cash (GCash, PayMaya)</option>
                        </select>
                    </div>
                    <div class="flex justify-center gap-3">
                        <button type="button" onclick="closePayAllLeaseModal()"
                            class="px-4 py-2 bg-white border border-gray-900 text-gray-900 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <i class="fa-solid fa-money-bill-wave mr-1"></i> Pay Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="noElectricBillModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-[10000]">
        <div
            class="relative top-20 mx-auto p-5 border border-yellow-400 w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fa-solid fa-lightbulb text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-bold text-gray-900 mt-2">Electric Bill Not Issued Yet</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-600">
                        <strong id="modal-payment-period" class="font-bold text-yellow-700"></strong>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        The electric bill for this payment period has not been issued by the administrator yet.
                        Please contact management to have the electric bill issued before proceeding with payment.
                    </p>
                    <p class="text-xs text-yellow-700 mt-3 bg-yellow-50 p-2 rounded">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Only payments with issued electric bills can be processed. Base rent payments require electric
                        bill assignment.
                    </p>
                </div>
                <div class="flex justify-center mt-4">
                    <button type="button" onclick="closeNoElectricBillModal()"
                        class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        OK, I Understand
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div id="missingElectricBillsModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-[10000]">
        <div
            class="relative top-20 mx-auto p-5 border border-yellow-400 w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-bold text-gray-900 mt-2">Missing Electric Bills</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-600">
                        <strong>Cannot process "Pay All" at this time.</strong>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        Some unpaid payments in this lease do not have electric bills issued yet.
                        All payments must have electric bills assigned before proceeding with bulk payment.
                    </p>
                    <p class="text-sm text-gray-600 mt-3 font-medium">
                        Please contact the administrator to issue missing electric bills for these periods:
                    </p>
                    <ul id="missing-periods-list"
                        class="text-left text-sm text-yellow-700 mt-2 space-y-1 max-h-48 overflow-y-auto">

                    </ul>
                    <p class="text-xs text-yellow-700 mt-3 bg-yellow-50 p-2 rounded">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        You may still pay individual payments that have electric bills issued.
                    </p>
                </div>
                <div class="flex justify-center mt-4">
                    <button type="button" onclick="closeMissingElectricBillsModal()"
                        class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    <style>
        .tab-link.active {
            border-bottom-color: #111827;
            color: #111827;
            font-weight: 700;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }


        #payments-content a:not(.bg-green-100):not(.bg-red-100):not(.bg-yellow-100) {
            color: #111827 !important;
            text-decoration: underline;
        }

        #payments-content a:not(.bg-green-100):not(.bg-red-100):not(.bg-yellow-100):hover {
            color: #374151 !important;
        }


        #payments-content a[href*="receipt"],
        #leases-content a[href*="leases"] {
            color: #111827 !important;
            text-decoration: underline;
        }

        #payments-content a[href*="receipt"]:hover,
        #leases-content a[href*="leases"]:hover {
            color: #374151 !important;
        }


        #payments-content a[href*="invoice"],
        #payments-content a[href*="/payments/"],
        #payments-content td a {
            color: #111827 !important;
            text-decoration: underline;
        }

        #payments-content a[href*="invoice"]:hover,
        #payments-content a[href*="/payments/"]:hover,
        #payments-content td a:hover {
            color: #374151 !important;
        }


        #payments-content .text-green-600,
        #payments-content .text-blue-600,
        #payments-content .text-indigo-600,
        #payments-content .text-purple-600,
        #leases-content .text-indigo-600 {
            color: #111827 !important;
        }

        #payments-content .text-green-600:hover,
        #payments-content .text-blue-600:hover,
        #payments-content .text-indigo-600:hover,
        #payments-content .text-purple-600:hover,
        #leases-content .text-indigo-600:hover {
            color: #374151 !important;
        }


        #payments-content button[class*="bg-purple"],
        #payments-content button[class*="bg-indigo"],
        #payments-content button[class*="bg-blue"] {
            background-color: #111827 !important;
            border-color: #111827 !important;
            color: white !important;
        }

        #payments-content button[class*="bg-purple"]:hover,
        #payments-content button[class*="bg-indigo"]:hover,
        #payments-content button[class*="bg-blue"]:hover {
            background-color: #1f2937 !important;
        }


        #payments-content button[onclick*="openPayAllModalForLease"] {
            background-color: #111827 !important;
            border-color: #111827 !important;
            color: white !important;
        }

        #payments-content button[onclick*="openPayAllModalForLease"]:hover {
            background-color: #1f2937 !important;
        }


        #payments-content a i,
        #leases-content a i {
            color: inherit !important;
        }


        #leases-content .border-green-200 {
            border-color: #bbf7d0 !important;
        }

        #leases-content .border-red-200 {
            border-color: #fecaca !important;
        }


        #payments-content tbody td:last-child a {
            color: #111827 !important;
            text-decoration: underline;
        }

        #payments-content tbody td:last-child a:hover {
            color: #374151 !important;
        }


        #payments-content .text-indigo-600.hover\:text-indigo-800,
        #payments-content .text-blue-600.hover\:text-blue-800 {
            color: #111827 !important;
            text-decoration: underline;
        }

        #payments-content .text-indigo-600.hover\:text-indigo-800:hover,
        #payments-content .text-blue-600.hover\:text-blue-800:hover {
            color: #374151 !important;
        }
    </style>

    <script>
        function getActiveTab() {
            const hash = window.location.hash.replace('#', '');
            return ['info', 'leases', 'payments'].includes(hash) ? hash : 'info';
        }

        function switchTab(evt, tabName) {
            document.querySelectorAll('.tab-content').forEach(panel => {
                panel.classList.add('hidden');
            });

            document.querySelectorAll('.tab-link').forEach(tab => {
                tab.classList.remove('active', 'text-gray-900', 'border-gray-900');
                tab.classList.add('text-gray-600', 'border-transparent');
            });

            document.getElementById('panel-' + tabName).classList.remove('hidden');

            if (evt && evt.currentTarget) {
                evt.currentTarget.classList.add('active', 'text-gray-900', 'border-gray-900');
                evt.currentTarget.classList.remove('text-gray-600', 'border-transparent');
            } else {
                const tabButton = document.querySelector(`[data-tab="${tabName}"]`);
                if (tabButton) {
                    tabButton.classList.add('active', 'text-gray-900', 'border-gray-900');
                    tabButton.classList.remove('text-gray-600', 'border-transparent');
                }
            }

            window.history.pushState(null, '', '#' + tabName);
        }



        function forceBlackTheme() {

            const paymentsContent = document.getElementById('payments-content');
            if (paymentsContent) {

                const links = paymentsContent.querySelectorAll('a');
                links.forEach(link => {

                    if (!link.classList.contains('bg-green-100') &&
                        !link.classList.contains('bg-red-100') &&
                        !link.classList.contains('bg-yellow-100')) {


                        link.classList.remove('text-green-600', 'text-blue-600', 'text-indigo-600',
                            'text-purple-600');
                        link.classList.remove('hover:text-green-800', 'hover:text-blue-800',
                            'hover:text-indigo-800', 'hover:text-purple-800');


                        link.classList.add('text-gray-900', 'hover:text-gray-700', 'underline');


                        link.style.color = '#111827';
                        link.style.textDecoration = 'underline';
                    }
                });


                const buttons = paymentsContent.querySelectorAll('button');
                buttons.forEach(button => {
                    if (button.classList.contains('bg-purple-600') ||
                        button.classList.contains('bg-indigo-600') ||
                        button.classList.contains('bg-blue-600')) {

                        button.classList.remove('bg-purple-600', 'bg-indigo-600', 'bg-blue-600');
                        button.classList.remove('hover:bg-purple-700', 'hover:bg-indigo-700', 'hover:bg-blue-700');
                        button.classList.add('bg-gray-900', 'hover:bg-gray-800');

                        button.style.backgroundColor = '#111827';
                        button.style.borderColor = '#111827';
                    }
                });
            }


            const leasesContent = document.getElementById('leases-content');
            if (leasesContent) {
                const links = leasesContent.querySelectorAll('a');
                links.forEach(link => {
                    if (!link.classList.contains('bg-green-100') &&
                        !link.classList.contains('bg-red-100')) {

                        link.classList.remove('text-indigo-600', 'hover:text-indigo-800');
                        link.classList.add('text-gray-900', 'hover:text-gray-700', 'underline');

                        link.style.color = '#111827';
                        link.style.textDecoration = 'underline';
                    }
                });
            }
        }


        document.addEventListener('DOMContentLoaded', function() {

            const leaseFilter = document.getElementById('lease-filter');
            if (leaseFilter) {
                leaseFilter.addEventListener('change', function() {
                    const tenantId = {{ $tenant->id }};
                    const filter = this.value;

                    fetch(`/tenants/${tenantId}/leases/filter-lease?filter=${filter}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('leases-content').innerHTML = data.html;

                            setTimeout(forceBlackTheme, 50);

                            const paymentFilter = document.getElementById('payment-lease-filter');
                            if (paymentFilter) {
                                paymentFilter.value = filter;
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            }


            const paymentLeaseFilter = document.getElementById('payment-lease-filter');
            if (paymentLeaseFilter) {
                paymentLeaseFilter.addEventListener('change', function() {
                    const tenantId = {{ $tenant->id }};
                    const filter = this.value;

                    fetch(`/tenants/${tenantId}/payments/filter-lease?filter=${filter}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('payments-content').innerHTML = data.html;

                            setTimeout(forceBlackTheme, 50);

                            const leaseFilter = document.getElementById('lease-filter');
                            if (leaseFilter) {
                                leaseFilter.value = filter;
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            }


            const activeTab = getActiveTab();
            switchTab(null, activeTab);


            forceBlackTheme();
        });



        function openPayAllModalForLease(leaseId, unpaidCount, totalAmount) {
            document.getElementById('modal-lease-id').value = leaseId;
            document.getElementById('modal-unpaid-count').textContent = unpaidCount;
            document.getElementById('modal-unpaid-count-2').textContent = unpaidCount;
            document.getElementById('modal-total-amount').textContent = new Intl.NumberFormat('en-PH', {
                minimumFractionDigits: 2
            }).format(totalAmount);
            document.getElementById('payAllLeaseModal').classList.remove('hidden');
            document.getElementById('payAllLeaseForm').action = `/leases/${leaseId}/pay-in-full`;
        }

        function closePayAllLeaseModal() {
            document.getElementById('payAllLeaseModal').classList.add('hidden');
        }


        function showNoElectricBillModal(paymentId, period) {
            document.getElementById('modal-payment-period').textContent = `Payment Period: ${period}`;
            document.getElementById('noElectricBillModal').classList.remove('hidden');
        }

        function closeNoElectricBillModal() {
            document.getElementById('noElectricBillModal').classList.add('hidden');
        }


        function showMissingElectricBillsModal(leaseId, buttonElement) {
            const missingPeriods = JSON.parse(buttonElement.getAttribute('data-missing-periods'));
            const listElement = document.getElementById('missing-periods-list');

            listElement.innerHTML = '';
            if (Array.isArray(missingPeriods) && missingPeriods.length > 0) {
                missingPeriods.forEach(period => {
                    const li = document.createElement('li');
                    li.className = 'flex items-start';
                    li.innerHTML = `<i class="fa-solid fa-circle text-yellow-500 mt-1 mr-2 text-xs"></i> ${period}`;
                    listElement.appendChild(li);
                });
            }

            document.getElementById('missingElectricBillsModal').classList.remove('hidden');
        }

        function closeMissingElectricBillsModal() {
            document.getElementById('missingElectricBillsModal').classList.add('hidden');
        }


        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closePayAllLeaseModal();
                closeNoElectricBillModal();
                closeMissingElectricBillsModal();
            }
        });
    </script>
</x-app-layout>

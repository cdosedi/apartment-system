@if ($filteredLeases->count())
    <div class="space-y-4">
        @foreach ($filteredLeases as $lease)
            <div
                class="bg-white border {{ $lease->status === 'active' ? 'border-green-200' : 'border-red-200' }} rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center">
                        <div class="bg-gray-100 p-3 rounded-lg mr-4">
                            <i class="fa-solid fa-door-open text-gray-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">Room {{ $lease->room->room_number }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $lease->start_date->format('M d, Y') }} → {{ $lease->end_date->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex-1 mt-4 md:mt-0">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium">Monthly Rent</p>
                                <p class="text-lg font-bold text-gray-900">₱{{ number_format($lease->monthly_rent, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-medium">Status</p>
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
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details <i class="fa-solid fa-arrow-right ml-1"></i>
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
            @if ($filter === 'all')
                No lease history found
            @else
                No lease found for this period
            @endif
        </h4>
        <p class="text-gray-500">
            @if ($filter === 'all')
                This tenant has no lease records.
            @else
                No lease matches the selected filter.
            @endif
        </p>
    </div>
@endif

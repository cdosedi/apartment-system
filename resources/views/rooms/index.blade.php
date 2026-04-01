<x-app-layout>
    <x-slot name="header">
        <div
            class="relative overflow-hidden bg-gradient-to-r from-gray-900 via-gray-800 to-black p-8 -mx-4 sm:-mx-8 shadow-2xl">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-bold mb-1 opacity-90">
                        Property Management
                    </p>
                    <h2 class="text-3xl font-black tracking-tighter text-white uppercase">
                        {{ __('Room Management') }}
                    </h2>
                </div>

                <div>
                    <a href="{{ route('rooms.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-all duration-200 shadow-lg shadow-indigo-500/20">
                        <i class="fa-solid fa-plus mr-2"></i> Add Room
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 text-green-700 p-3 rounded-md border border-green-100">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($rooms->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($rooms as $room)
                                <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow">

                                    <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-4 border-b">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-2xl font-black tracking-tighter text-white uppercase">
                                                    Room {{ $room->room_number }}
                                                </h3>
                                                <p class="text-sm text-gray-400 mt-1">
                                                    {{ $room->bed_capacity }}
                                                    bed{{ $room->bed_capacity > 1 ? 's' : '' }} capacity
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                @if ($room->available_beds > 0)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                                        <i class="fa-solid fa-circle-check mr-1"></i> Available
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                                        <i class="fa-solid fa-bed mr-1"></i> Full
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-gray-50 border-b">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Occupied:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $room->activeLeases()->count() }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm mt-1">
                                            <span class="text-gray-600">Available:</span>
                                            <span class="font-medium text-green-600">{{ $room->available_beds }}</span>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        @if ($room->activeLeases->count() > 0)
                                            <h4
                                                class="text-[10px] uppercase tracking-widest font-bold text-gray-400 mb-3">
                                                Current Tenants</h4>
                                            <div class="space-y-2">
                                                @foreach ($room->activeLeases as $lease)
                                                    <div
                                                        class="flex items-center justify-between p-2 bg-white rounded border border-gray-100 hover:bg-gray-50 transition-colors">
                                                        <div class="flex items-center">
                                                            <div
                                                                class="h-8 w-8 bg-indigo-50 text-indigo-600 rounded flex items-center justify-center text-xs font-bold mr-3">
                                                                {{ strtoupper(substr($lease->tenant->full_name, 0, 1)) }}
                                                            </div>
                                                            <a href="{{ route('tenants.show', $lease->tenant) }}"
                                                                class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                                                {{ $lease->tenant->full_name }}
                                                            </a>
                                                        </div>
                                                        <span class="text-[10px] text-gray-500">
                                                            {{ $lease->start_date->format('M Y') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <p class="text-xs text-gray-400 italic">No tenants assigned</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4 bg-gray-50 border-t flex justify-end space-x-2">
                                        <form action="{{ route('rooms.add-bed', $room) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-white border border-blue-200 rounded-md hover:bg-blue-50 transition-colors">
                                                <i class="fa-solid fa-plus mr-1"></i> Add Bed
                                            </button>
                                        </form>

                                        @if ($room->bed_capacity > 1 && $room->available_beds > 0)
                                            <form action="{{ route('rooms.remove-bed', $room) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-white border border-red-200 rounded-md hover:bg-blue-50 transition-colors"
                                                    onclick="return confirm('Remove a bed from Room {{ $room->room_number }}?')">
                                                    <i class="fa-solid fa-minus mr-1"></i> Remove Bed
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $rooms->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-300 mb-4">
                                <i class="fa-solid fa-door-open text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No rooms found</h3>
                            <p class="text-gray-500 mt-2">Create your first room to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

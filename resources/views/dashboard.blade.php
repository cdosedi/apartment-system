<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.3);
            }

            50% {
                box-shadow: 0 0 20px 5px rgba(16, 185, 129, 0.2);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .animate-fade-up {
            opacity: 0;
            animation: fade-in-up 0.6s ease-out forwards;
        }

        .animate-fade-in {
            opacity: 0;
            animation: fade-in 0.8s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        .delay-400 {
            animation-delay: 400ms;
        }

        .delay-500 {
            animation-delay: 500ms;
        }

        .pulse-glow {
            animation: pulse-glow 3s infinite;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f4f4f5;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #d4d4d8;
            border-radius: 20px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #a1a1aa;
        }
    </style>

    <div class="py-10 bg-gradient-to-br from-zinc-50 via-white to-zinc-50 min-h-screen relative overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float">
            </div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"
                style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

            <div class="mb-10 flex flex-col md:flex-row justify-between items-end gap-6 animate-fade-up">
                <div>
                    <div class="flex items-center gap-2 mb-1 group">
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse group-hover:scale-150 transition-transform"></span>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-400">Operations Live •
                            {{ now()->format('Y') }}</p>
                    </div>
                    <h2
                        class="text-4xl font-extralight text-black tracking-tighter hover:tracking-tight transition-all duration-300">
                        Property Analytics</h2>
                </div>
                <a href="{{ route('reports.income') }}"
                    class="group bg-white/80 backdrop-blur-sm border border-zinc-200 px-6 py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:shadow-xl transition-all flex items-center gap-2 text-zinc-600 hover:border-emerald-200 hover:text-emerald-600">
                    <i class="fa-solid fa-chart-line group-hover:rotate-12 transition-transform"></i> Full Income Report
                </a>
            </div>


            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="animate-fade-up delay-100">
                    <div
                        class="group bg-gradient-to-br from-white to-zinc-50 border border-zinc-200 p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-zinc-100 rounded-full -mr-10 -mt-10 transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-50">
                        </div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Total Units</p>
                                <i
                                    class="fa-solid fa-door-open text-zinc-300 group-hover:text-emerald-400 transition-colors text-xs"></i>
                            </div>
                            <p class="text-3xl font-medium text-black">{{ \App\Models\Room::count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-200">
                    <div
                        class="group bg-gradient-to-br from-white to-emerald-50/30 border border-emerald-100 p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-emerald-100 rounded-full -mr-10 -mt-10 transition-all duration-500 group-hover:scale-150">
                        </div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <p class="text-[9px] font-black text-emerald-600/50 uppercase tracking-widest">Active
                                    Leases</p>
                                <i
                                    class="fa-solid fa-file-signature text-emerald-200 group-hover:text-emerald-400 transition-colors text-xs"></i>
                            </div>
                            <p class="text-3xl font-medium text-emerald-600">
                                {{ \App\Models\Lease::where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-300">
                    <div
                        class="group bg-gradient-to-br from-white to-zinc-50 border border-zinc-200 p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-zinc-100 rounded-full -mr-10 -mt-10 transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-50">
                        </div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Occupied Beds
                                </p>
                                <i
                                    class="fa-solid fa-bed text-zinc-300 group-hover:text-blue-400 transition-colors text-xs"></i>
                            </div>
                            <p class="text-3xl font-medium text-black">{{ $occupiedBeds }} <span
                                    class="text-sm text-zinc-400 font-normal">/ {{ $totalBeds }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-400">
                    <div
                        class="group bg-zinc-900 p-6 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden pulse-glow">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-zinc-800 rounded-full -mr-10 -mt-10 transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-800/50">
                        </div>
                        <div class="relative">
                            <div class="flex justify-between items-start mb-4">
                                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Occupancy</p>
                                <i
                                    class="fa-solid fa-chart-pie text-zinc-700 group-hover:text-emerald-400 transition-colors text-xs"></i>
                            </div>
                            <p class="text-3xl font-medium text-white">{{ number_format($occupancyRate, 0) }}%</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-10">
                <div class="animate-fade-up delay-100">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-zinc-200 p-5 rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-16 h-16 bg-zinc-50 rounded-full -mr-8 -mt-8 transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-50">
                        </div>
                        <div class="relative">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Rent Collected
                            </p>
                            <p class="text-xl font-bold text-zinc-900 tracking-tight">
                                ₱{{ number_format($totalRent, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-200">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-zinc-200 p-5 rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-16 h-16 bg-blue-50 rounded-full -mr-8 -mt-8 transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-100">
                        </div>
                        <div class="relative">
                            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Utility
                                Recovery</p>
                            <p class="text-xl font-bold text-zinc-900 tracking-tight">
                                ₱{{ number_format($totalElectricCollected, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-300">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-rose-100 p-5 rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-16 h-16 bg-rose-50 rounded-full -mr-8 -mt-8 transition-all duration-500 group-hover:scale-150 group-hover:bg-rose-100">
                        </div>
                        <div class="relative">
                            <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest mb-1">Utility
                                Expenses</p>
                            <p class="text-xl font-bold text-rose-600 tracking-tight">
                                ₱{{ number_format($actualUtilityExpense, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-400">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-amber-100 p-5 rounded-3xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-16 h-16 bg-amber-50 rounded-full -mr-8 -mt-8 transition-all duration-500 group-hover:scale-150 group-hover:bg-amber-100">
                        </div>
                        <div class="relative">
                            <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">Total
                                Receivables</p>
                            <p class="text-xl font-bold text-amber-700 tracking-tight">
                                ₱{{ number_format($totalReceivables, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="animate-fade-up delay-500">
                    <div
                        class="bg-emerald-600 p-5 rounded-3xl shadow-lg shadow-emerald-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-16 h-16 bg-emerald-500 rounded-full -mr-8 -mt-8 transition-all duration-700 group-hover:scale-[3] group-hover:bg-emerald-400/30">
                        </div>
                        <div class="relative">
                            <p class="text-[9px] font-black text-emerald-100 uppercase tracking-widest mb-1">Net Profit
                            </p>
                            <p class="text-xl font-bold text-white tracking-tight">
                                ₱{{ number_format($totalProfit, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <div
                    class="bg-white/80 backdrop-blur-sm border border-zinc-200 rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 animate-fade-up">
                    <h3
                        class="text-[10px] font-black uppercase tracking-widest text-zinc-800 mb-6 flex items-center gap-2">
                        <span class="w-1 h-4 bg-emerald-400 rounded-full"></span> Revenue Mix (Last 6 Months)
                    </h3>
                    <div class="h-72"><canvas id="revenueChart"></canvas></div>
                </div>
                <div
                    class="bg-zinc-900 rounded-[2.5rem] p-8 border border-zinc-800 shadow-2xl hover:shadow-emerald-900/20 transition-all duration-500 animate-fade-up delay-200">
                    <h3
                        class="text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-6 flex items-center gap-2">
                        <span class="w-1 h-4 bg-emerald-500 rounded-full"></span> Profit Trend Line
                    </h3>
                    <div class="h-72"><canvas id="profitChart"></canvas></div>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                <div class="animate-fade-up delay-100">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-zinc-200 rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                        <div class="px-8 py-6 bg-zinc-50/50 border-b border-zinc-100 flex justify-between items-center">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-black">Top 10 Delinquents
                            </h3>
                            <i class="fa-solid fa-triangle-exclamation text-amber-400 animate-pulse"></i>
                        </div>
                        <div class="divide-y divide-zinc-50 flex-grow max-h-[450px] overflow-y-auto custom-scroll">
                            @forelse($topDelinquents as $index => $d)
                                <div class="p-6 hover:bg-zinc-50 transition-all group relative">
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-1 bg-rose-400 scale-y-0 group-hover:scale-y-100 transition-transform origin-top">
                                    </div>
                                    <div class="flex justify-between items-start mb-1">
                                        <p
                                            class="text-sm font-bold text-black group-hover:text-emerald-600 transition-colors">
                                            {{ $d->tenant_name }}</p>
                                        <p class="text-sm font-black text-rose-600">
                                            ₱{{ number_format($d->total_debt, 2) }}</p>
                                    </div>
                                    <p class="text-[10px] text-zinc-400 uppercase font-bold tracking-tight">Room
                                        {{ $d->room_number }} • {{ $d->missed_count }} missed bills</p>
                                </div>
                            @empty
                                <div class="p-12 text-center text-zinc-300 text-[10px] font-black uppercase">No
                                    outstanding balances</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 animate-fade-up delay-200">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-zinc-200 rounded-[2rem] p-8 shadow-sm hover:shadow-xl transition-all duration-300">
                        <h3
                            class="text-[10px] font-black uppercase tracking-widest text-zinc-800 mb-8 flex items-center gap-2">
                            <span class="w-1 h-4 bg-emerald-400 rounded-full"></span> Live Room Capacity Map
                        </h3>
                        <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-4">
                            @foreach ($roomMap as $room)
                                <div class="group perspective">
                                    <div
                                        class="p-4 rounded-[1.5rem] border transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:rotate-1 {{ $room->active_leases_count == $room->bed_capacity ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-100 bg-zinc-50/50 text-zinc-900' }} text-center">
                                        <p class="text-[10px] font-black mb-2">{{ $room->room_number }}</p>
                                        <div class="flex justify-center gap-1">
                                            @for ($i = 0; $i < $room->active_leases_count; $i++)
                                                <div
                                                    class="w-1 h-3 {{ $room->active_leases_count == $room->bed_capacity ? 'bg-white' : 'bg-black' }} rounded-full transition-all duration-300 group-hover:scale-150">
                                                </div>
                                            @endfor
                                            @for ($i = 0; $i < $room->bed_capacity - $room->active_leases_count; $i++)
                                                <div
                                                    class="w-1 h-3 {{ $room->active_leases_count == $room->bed_capacity ? 'bg-zinc-700' : 'bg-zinc-200' }} rounded-full transition-all duration-300 group-hover:bg-emerald-200">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            @if (count($utilityLossRooms) > 0)
                <div class="mt-8 mb-12 animate-fade-up delay-300">
                    <div
                        class="bg-white/80 backdrop-blur-sm border border-amber-200 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden relative group">
                        <div class="absolute top-0 left-0 w-2 h-full bg-amber-400 group-hover:w-3 transition-all">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-amber-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative">
                            <div class="flex items-center gap-4">
                                <div
                                    class="bg-amber-100 p-4 rounded-2xl text-amber-600 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-bolt-lightning text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black uppercase text-black">Utility Recovery Gap</h4>
                                    <p class="text-xs text-zinc-500">Rooms costing more in electricity than collected
                                        this month.</p>
                                </div>
                            </div>
                            <div class="flex gap-2 flex-wrap justify-center">
                                @foreach ($utilityLossRooms as $loss)
                                    <div
                                        class="px-5 py-2.5 bg-zinc-50 border border-zinc-100 rounded-2xl text-center hover:border-amber-400 transition-all hover:scale-105 hover:shadow-md">
                                        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-tighter">RM
                                            {{ $loss->room_number }}</p>
                                        <p class="text-xs font-bold text-rose-600">
                                            -₱{{ number_format($loss->loss, 0) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartFont = {
            family: "'Plus Jakarta Sans', sans-serif",
            weight: '700',
            size: 10
        };


        const revData = @json($lastSixMonths);
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revData.map(d => d.month),
                datasets: [{
                        label: 'Rent',
                        data: revData.map(d => d.rent),
                        backgroundColor: '#18181b',
                        borderRadius: 4,
                        barThickness: 16
                    },
                    {
                        label: 'Utilities',
                        data: revData.map(d => d.utilities),
                        backgroundColor: '#f4f4f5',
                        borderRadius: 4,
                        barThickness: 16
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: chartFont,
                            color: '#a1a1aa'
                        }
                    },
                    y: {
                        border: {
                            display: false
                        },
                        grid: {
                            color: '#f8f8f8'
                        },
                        ticks: {
                            font: chartFont,
                            color: '#a1a1aa'
                        }
                    }
                }
            }
        });


        const profitData = @json($ytdData);
        new Chart(document.getElementById('profitChart'), {
            type: 'line',
            data: {
                labels: profitData.map(d => d.month),
                datasets: [{
                    data: profitData.map(d => d.profit),
                    borderColor: '#10b981',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    pointRadius: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#71717a',
                            font: chartFont
                        }
                    },
                    y: {
                        border: {
                            display: false
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.03)'
                        },
                        ticks: {
                            color: '#71717a',
                            font: chartFont
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>

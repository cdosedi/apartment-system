<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-[100] shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform active:scale-95">
                        <img src="{{ asset('images/casa_oro_logo.png') }}" alt="Casa Oro" class="h-14 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-1 ms-10 sm:flex items-center">
                    @php
                        $navItems = [
                            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
                            ['name' => 'Admins', 'route' => 'admins.index', 'icon' => 'admin_panel_settings'],
                            ['name' => 'Tenants', 'route' => 'tenants.index', 'icon' => 'group'],
                            ['name' => 'Rooms', 'route' => 'rooms.index', 'icon' => 'meeting_room'],
                            ['name' => 'Electric', 'route' => 'electric-bills.index', 'icon' => 'bolt'],
                            ['name' => 'Reports', 'route' => 'reports.income', 'icon' => 'payments'],
                        ];
                    @endphp

                    @foreach ($navItems as $item)
                        @php $isActive = request()->routeIs($item['route'].'*'); @endphp
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center px-4 py-2.5 text-[11px] font-black uppercase tracking-widest transition-all duration-200 rounded-xl
                                  {{ $isActive ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span
                                class="material-symbols-rounded text-[18px] mr-2 {{ $isActive ? 'text-amber-400' : 'text-slate-400' }}">
                                {{ $item['icon'] }}
                            </span>
                            {{ __($item['name']) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center gap-3 px-4 py-2 border border-slate-200 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-all group">
                            <div class="flex flex-col text-right">
                                <span
                                    class="text-xs font-black text-slate-800 tracking-tight">{{ Auth::user()->name }}</span>
                                <span
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">Authorized</span>
                            </div>
                            <div
                                class="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center text-white text-sm font-black shadow-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Settings
                            </p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')"
                            class="flex items-center py-3 text-xs font-bold text-slate-700">
                            <span class="material-symbols-rounded text-[18px] mr-2 text-slate-400">person</span>
                            {{ __('My Profile') }}
                        </x-dropdown-link>

                        <div class="border-t border-slate-50"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center py-3 text-xs font-bold text-rose-600 hover:bg-rose-50">
                                <span class="material-symbols-rounded text-[18px] mr-2 text-rose-400">logout</span>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-3 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-900 hover:text-white transition-all">
                    <span class="material-symbols-rounded" x-show="!open">menu</span>
                    <span class="material-symbols-rounded" x-show="open" x-cloak>close</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        class="sm:hidden bg-white border-t border-slate-100 pb-6 shadow-inner">
        <div class="pt-4 space-y-2 px-4">
            @foreach ($navItems as $item)
                @php $isActive = request()->routeIs($item['route'].'*'); @endphp
                <a href="{{ route($item['route']) }}"
                    class="flex items-center px-4 py-4 text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl
                          {{ $isActive ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 bg-slate-50' }}">
                    <span
                        class="material-symbols-rounded text-[20px] mr-4 {{ $isActive ? 'text-amber-400' : 'text-slate-400' }}">
                        {{ $item['icon'] }}
                    </span>
                    {{ __($item['name']) }}
                </a>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t border-slate-100 px-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-black text-sm text-slate-800 tracking-tight">{{ Auth::user()->name }}</div>
                    <div class="font-bold text-[10px] text-slate-400 uppercase tracking-tighter">
                        {{ Auth::user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-4 rounded-2xl bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-100">
                    <span class="material-symbols-rounded text-[18px]">logout</span>
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>

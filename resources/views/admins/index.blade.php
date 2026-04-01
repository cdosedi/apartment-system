<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div
            class="relative overflow-hidden bg-gradient-to-r from-slate-800 via-slate-900 to-black p-8 -mx-4 sm:-mx-8 shadow-2xl">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-shield-halved text-amber-400 text-xs"></i>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Security &
                            Access</span>
                    </div>
                    <h2 class="font-black text-3xl text-white tracking-tighter">
                        {{ __('Admin Management') }}
                    </h2>
                    <p class="text-sm font-medium text-slate-400 mt-1">
                        Control system access and administrative privileges
                    </p>
                </div>

                <a href="{{ route('admins.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-400 transition-all shadow-xl active:scale-95 group">
                    <i class="fa-solid fa-plus-circle mr-2 group-hover:rotate-90 transition-transform"></i>
                    Add System Admin
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 flex gap-4 overflow-x-auto pb-2">
                <div
                    class="bg-white px-6 py-4 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 min-w-[200px]">
                    <div class="h-10 w-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Total Admins</p>
                        <p class="text-lg font-black text-slate-800">{{ $admins->count() }}</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl shadow-sm text-sm text-emerald-700 font-medium animate-pulse">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                    Identity</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                    Contact Email</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                    Account Status</th>
                                <th
                                    class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                    Control</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($admins as $admin)
                                <tr
                                    class="hover:bg-slate-50/50 transition-colors group {{ $admin->trashed() ? 'opacity-60 italic' : '' }}">
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-11 w-11 {{ $admin->trashed() ? 'bg-slate-200' : 'bg-slate-800' }} text-white rounded-2xl flex items-center justify-center font-bold text-sm shadow-inner transition-transform group-hover:scale-105">
                                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-black text-slate-800">{{ $admin->name }}</p>
                                                <p
                                                    class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                                    System Administrator</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-sm font-medium text-slate-500">
                                        {{ $admin->email }}
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        @if ($admin->trashed())
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-100">
                                                <i class="fa-solid fa-lock mr-1.5"></i> Deactivated
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                <i class="fa-solid fa-circle-check mr-1.5"></i> Fully Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            @if ($admin->trashed())
                                                <form action="{{ route('admins.restore', $admin->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 hover:text-white transition-all">
                                                        Restore Access
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admins.edit', $admin) }}"
                                                    class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-800 hover:text-white transition-all shadow-sm">
                                                    <i class="fa-solid fa-pen-nib text-xs"></i>
                                                </a>
                                                <form action="{{ route('admins.destroy', $admin) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Revoke admin access?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 text-rose-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                                        <i class="fa-solid fa-power-off text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="h-20 w-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mb-4 text-slate-200">
                                                <i class="fa-solid fa-user-secret text-4xl"></i>
                                            </div>
                                            <p class="text-xs font-black text-slate-300 uppercase tracking-[0.3em]">No
                                                administrators registered</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

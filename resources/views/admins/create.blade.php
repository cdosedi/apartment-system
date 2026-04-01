<x-app-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <x-slot name="header">
        <div
            class="relative overflow-hidden bg-gradient-to-r from-slate-800 via-slate-900 to-black p-8 -mx-4 sm:-mx-8 shadow-2xl">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-user-shield text-amber-400 text-xs"></i>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Access
                            Control</span>
                    </div>
                    <h2 class="font-black text-3xl text-white tracking-tighter">
                        {{ __('Register New Admin') }}
                    </h2>
                    <p class="text-sm font-medium text-slate-400 mt-1">
                        Initialize a new administrative account with system-wide privileges
                    </p>
                </div>

                <a href="{{ route('admins.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-700/50 backdrop-blur-md border border-slate-600 rounded-xl font-bold text-[10px] text-slate-300 uppercase tracking-widest hover:bg-slate-700 hover:text-white transition-all">
                    <i class="fa-solid fa-xmark mr-2"></i> Discard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-10 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Credential Setup</h3>
                    <div
                        class="h-8 w-8 bg-amber-400 text-slate-900 rounded-lg flex items-center justify-center text-xs">
                        <i class="fa-solid fa-key"></i>
                    </div>
                </div>

                <div class="p-10">
                    <form method="POST" action="{{ route('admins.store') }}" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2">
                                <label for="name"
                                    class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Full
                                    Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    placeholder="Enter complete name"
                                    class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-slate-900/5 transition-all @error('name') ring-2 ring-red-500 @enderror"
                                    required>
                                @error('name')
                                    <p class="mt-2 text-xs text-red-600 font-bold uppercase tracking-tighter italic">
                                        {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="email"
                                    class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Work
                                    Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    placeholder="admin@example.com"
                                    class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-slate-900/5 transition-all @error('email') ring-2 ring-red-500 @enderror"
                                    required>
                                @error('email')
                                    <p class="mt-2 text-xs text-red-600 font-bold uppercase tracking-tighter italic">
                                        {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 border-t border-slate-50 pt-4">
                                <div class="flex items-center gap-4">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Security
                                        Credentials</h4>
                                    <div class="h-px flex-1 bg-slate-50"></div>
                                </div>
                            </div>

                            <div>
                                <label for="password"
                                    class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                                <input type="password" name="password" id="password"
                                    class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-slate-900/5 transition-all @error('password') ring-2 ring-red-500 @enderror"
                                    required minlength="8">
                                @error('password')
                                    <p class="mt-2 text-xs text-red-600 font-bold uppercase tracking-tighter italic">
                                        {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Verify
                                    Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 focus:ring-4 focus:ring-slate-900/5 transition-all"
                                    required>
                            </div>
                        </div>

                        <div class="pt-10 flex flex-col md:flex-row items-center justify-end gap-4">
                            <a href="{{ route('admins.index') }}"
                                class="w-full md:w-auto text-center px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-800 transition-colors">
                                Return to List
                            </a>
                            <button type="submit"
                                class="w-full md:w-auto px-10 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black hover:shadow-2xl hover:shadow-slate-200 transition-all active:scale-95">
                                <i class="fa-solid fa-shield-plus mr-2"></i> Authorize Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 text-center flex items-center justify-center gap-3">
                <i class="fa-solid fa-circle-info text-slate-300 text-xs"></i>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                    New accounts are created with immediate effect and logged in the security audit trail.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>

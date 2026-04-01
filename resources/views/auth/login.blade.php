<x-guest-layout>
    <div :class="expanded ? 'sm:w-1/2' : 'w-full'"
        class="flex flex-col items-center justify-center p-8 sm:p-12 bg-white border-r border-gray-50 h-full transition-all duration-[850ms]"
        style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">

        <div class="w-full max-w-[280px] flex flex-col items-center">
            <div class="transition-transform duration-700 hover:scale-[1.02]">
                <x-application-logo class="w-full h-auto mx-auto" />
            </div>

            <div class="mt-8 flex flex-col items-center">
                <div class="h-px w-8 bg-[#855C1B] mb-4"></div>
                <h1 class="text-[8px] font-black uppercase tracking-[0.6em] text-gray-300 text-center">
                    Admin Terminal
                </h1>
            </div>

            <button x-show="!expanded" @click="expanded = true"
                x-transition:enter="transition opacity duration-500 delay-300"
                class="mt-10 bg-[#855C1B] text-white py-3 px-10 text-[9px] font-black uppercase tracking-[0.5em] transition-all rounded-sm shadow-xl active:scale-95 hover:bg-black">
                Enter Portal
            </button>
        </div>
    </div>

    <div x-show="expanded" x-transition:enter="transition all duration-[700ms] delay-[200ms] ease-out"
        x-transition:enter-start="opacity-0 transform translate-x-8"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        class="w-full sm:w-1/2 bg-[#0D0D0D] flex flex-col justify-center p-8 sm:p-16 h-full relative">

        <div class="max-w-sm w-full mx-auto relative z-10">
            <header class="mb-8 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-light text-white uppercase tracking-[0.2em]">
                        System <span class="font-black text-[#855C1B]">Login</span>
                    </h2>
                    <div class="h-[1px] w-8 bg-[#855C1B] mt-3"></div>
                </div>
                <button @click="expanded = false" class="text-gray-600 hover:text-white transition-colors">
                    <span class="material-symbols-rounded text-sm">close</span>
                </button>
            </header>

            <x-auth-session-status class="mb-4 text-[#855C1B] text-[10px] font-bold" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div class="relative group">
                    <label
                        class="text-[7px] font-black uppercase tracking-[0.3em] text-gray-600 mb-1 block">Identifier</label>
                    <div
                        class="flex items-center border-b border-gray-800 group-focus-within:border-[#855C1B] py-1 transition-all">
                        <span
                            class="material-symbols-rounded text-gray-600 group-focus-within:text-[#855C1B] text-lg mr-2">person</span>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="w-full bg-transparent border-none text-white text-sm focus:ring-0 p-0 placeholder-gray-800"
                            placeholder="Email Address" />
                    </div>
                </div>

                <div class="relative group">
                    <label class="text-[7px] font-black uppercase tracking-[0.3em] text-gray-600 mb-1 block">Access
                        Key</label>
                    <div
                        class="flex items-center border-b border-gray-800 group-focus-within:border-[#855C1B] py-1 transition-all">
                        <span
                            class="material-symbols-rounded text-gray-600 group-focus-within:text-[#855C1B] text-lg mr-2">lock</span>
                        <input id="password" type="password" name="password" required
                            class="w-full bg-transparent border-none text-white text-sm focus:ring-0 p-0 placeholder-gray-800"
                            placeholder="Password" />
                    </div>
                </div>

                <div class="flex items-center justify-between text-[8px] font-bold uppercase tracking-widest">
                    <label class="flex items-center cursor-pointer text-gray-600 hover:text-gray-400 group">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="bg-transparent border-gray-800 text-[#855C1B] focus:ring-0 w-3 h-3 rounded-none transition-colors">
                        <span class="ml-2">Remember</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-gray-600 hover:text-[#855C1B] transition-colors">Recovery</a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full bg-[#855C1B] hover:bg-white hover:text-black text-white py-4 text-[9px] font-black uppercase tracking-[0.5em] transition-all rounded-sm shadow-xl">
                    Confirm Access
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

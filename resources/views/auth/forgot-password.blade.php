<x-guest-layout>
    <div
        class="w-full sm:w-1/2 flex flex-col items-center justify-center p-8 sm:p-12 bg-white border-r border-gray-50 h-full">
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
        </div>
    </div>

    <div x-init="expanded = true"
        class="w-full sm:w-1/2 bg-[#0D0D0D] flex flex-col justify-center p-8 sm:p-16 h-full relative">

        <div class="max-w-sm w-full mx-auto relative z-10">
            <header class="mb-8 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-light text-white uppercase tracking-[0.2em]">
                        Reset <span class="font-black text-[#855C1B]">Request</span>
                    </h2>
                    <div class="h-[1px] w-8 bg-[#855C1B] mt-3"></div>
                </div>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-white transition-colors">
                    <span class="material-symbols-rounded text-sm">close</span>
                </a>
            </header>

            <div class="mb-8 text-[9px] uppercase tracking-[0.25em] text-gray-500 leading-loose">
                {{ __('Secure credential recovery. Provide your identifier to receive a synchronization link.') }}
            </div>

            <x-auth-session-status class="mb-6 text-[#855C1B] text-[10px] font-bold" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-10">
                @csrf

                <div class="relative group">
                    <label
                        class="text-[7px] font-black uppercase tracking-[0.3em] text-gray-600 mb-1 block">Identifier</label>
                    <div
                        class="flex items-center border-b border-gray-800 group-focus-within:border-[#855C1B] py-1 transition-all">
                        <span
                            class="material-symbols-rounded text-gray-600 group-focus-within:text-[#855C1B] text-lg mr-2">mail</span>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="w-full bg-transparent border-none text-white text-sm focus:ring-0 p-0 placeholder-gray-800"
                            placeholder="admin@casaoro.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-[8px] uppercase tracking-tighter text-red-500" />
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-[#855C1B] hover:bg-white hover:text-black text-white py-4 text-[9px] font-black uppercase tracking-[0.5em] transition-all rounded-sm shadow-xl">
                        Send Reset Link
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}"
                        class="text-[8px] font-black uppercase tracking-[0.4em] text-gray-500 hover:text-[#855C1B] transition-colors">
                        Return to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

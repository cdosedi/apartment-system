<x-app-layout>
    <div class="bg-white min-h-screen pb-20">
        <div class="border-b border-gray-100 py-4 mb-12">
            <div class="max-w-3xl mx-auto px-6 flex justify-between items-center">
                <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-gray-500">Step 01 / Tenant</h2>
                <a href="{{ route('tenants.index') }}" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </a>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-6">
            <x-modern-progress :currentStep="1" :progress="25" />

            <div class="mt-16">
                <div class="mb-10 text-center">
                    <h1 class="text-3xl font-light text-gray-900 tracking-tight">Personal Information</h1>
                    <p class="text-gray-500 mt-2 font-light">Enter the primary resident's details below.</p>
                </div>

                <form method="POST" action="{{ route('tenants.lease-form') }}" class="space-y-12">
                    @csrf

                    <div class="space-y-8">
                        <div
                            class="group relative border-b border-gray-200 focus-within:border-blue-600 transition-all duration-300">
                            <label for="full_name"
                                class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Full
                                Legal Name</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                                class="w-full border-none px-0 py-2 focus:ring-0 text-xl text-gray-900 placeholder-gray-400 font-light"
                                placeholder="e.g. Maria Labo" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div
                                class="group relative border-b border-gray-200 focus-within:border-blue-600 transition-all duration-300">
                                <label for="email"
                                    class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Email
                                    Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="w-full border-none px-0 py-2 focus:ring-0 text-lg text-gray-900 placeholder-gray-400 font-light"
                                    placeholder="marialabo@domain.com">
                            </div>
                            <div
                                class="group relative border-b border-gray-200 focus-within:border-blue-600 transition-all duration-300">
                                <label for="contact_number"
                                    class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Mobile
                                    Number</label>
                                <input type="text" name="contact_number" id="contact_number"
                                    value="{{ old('contact_number') }}"
                                    class="w-full border-none px-0 py-2 focus:ring-0 text-lg text-gray-900 placeholder-gray-400 font-light"
                                    placeholder="0917 000 0000" required>
                            </div>
                        </div>

                        <div
                            class="group relative border-b border-gray-200 focus-within:border-blue-600 transition-all duration-300">
                            <label for="address"
                                class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Home
                                Address</label>
                            <textarea name="address" id="address" rows="1"
                                class="w-full border-none px-0 py-2 focus:ring-0 text-lg text-gray-900 placeholder-gray-400 font-light resize-none"
                                placeholder="City, State, Zip" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50/80 rounded-3xl p-8 space-y-8 border border-gray-100">
                        <h4 class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-500 text-center">Emergency
                            Protocol</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div
                                class="group relative border-b border-gray-200 focus-within:border-blue-600 transition-all duration-300">
                                <label
                                    class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Contact
                                    Name</label>
                                <input type="text" name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name') }}"
                                    class="w-full border-none bg-transparent px-0 py-2 focus:ring-0 text-base text-gray-900 font-medium placeholder-gray-400"
                                    placeholder="Full Name" required>
                            </div>
                            <div
                                class="group relative border-b border-gray-200 focus-within:border-blue-600 transition-all duration-300">
                                <label
                                    class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Contact
                                    Phone</label>
                                <input type="text" name="emergency_contact_number"
                                    value="{{ old('emergency_contact_number') }}"
                                    class="w-full border-none bg-transparent px-0 py-2 focus:ring-0 text-base text-gray-900 font-medium placeholder-gray-400"
                                    placeholder="Phone Number" required>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 flex flex-col items-center">
                        <button type="submit"
                            class="group relative flex items-center justify-center w-full md:w-64 py-5 bg-gray-900 text-white rounded-full overflow-hidden transition-all hover:bg-black active:scale-95 shadow-xl shadow-gray-200">
                            <span class="relative z-10 font-bold tracking-widest uppercase text-xs">Continue</span>
                            <div
                                class="absolute inset-0 bg-blue-600 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            </div>
                        </button>
                        <p class="mt-6 text-xs text-gray-400 uppercase tracking-widest font-medium">Safe & Encrypted
                            Session</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

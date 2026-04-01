<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $step === 'form' ? __('Create Tenant') : __('Review Tenant Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    @if ($step === 'form')

                        <form method="POST" action="{{ route('tenants.preview') }}">
                            @csrf
                            @if ($errors->any())
                                <div class="mb-4 bg-red-50 text-red-700 p-3 rounded-md">
                                    <strong>Whoops! Something went wrong.</strong>
                                    <ul class="mt-2 list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full
                                        Name</label>
                                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('full_name') border-red-300 @enderror"
                                        required>
                                    @error('full_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email
                                        (Optional)</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact
                                        Number</label>
                                    <input type="text" name="contact_number" id="contact_number"
                                        value="{{ old('contact_number') }}" placeholder="09123456789"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('contact_number') border-red-300 @enderror"
                                        required>
                                    @error('contact_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address"
                                        class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea name="address" id="address" rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('address') border-red-300 @enderror"
                                        required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emergency_contact_name"
                                        class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                        value="{{ old('emergency_contact_name') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('emergency_contact_name') border-red-300 @enderror"
                                        required>
                                    @error('emergency_contact_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emergency_contact_number"
                                        class="block text-sm font-medium text-gray-700">Emergency Contact Number</label>
                                    <input type="text" name="emergency_contact_number" id="emergency_contact_number"
                                        value="{{ old('emergency_contact_number') }}" placeholder="09213456789"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('emergency_contact_number') border-red-300 @enderror"
                                        required>
                                    @error('emergency_contact_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="{{ route('tenants.index') }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Review Information
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="space-y-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Review Tenant Information</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Full Name</span>
                                        <p class="text-gray-900">{{ $previewData['full_name'] }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Email</span>
                                        <p class="text-gray-900">{{ $previewData['email'] ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Contact Number</span>
                                        <p class="text-gray-900">{{ $previewData['contact_number'] }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Emergency Contact</span>
                                        <p class="text-gray-900">{{ $previewData['emergency_contact_name'] }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Emergency Number</span>
                                        <p class="text-gray-900">{{ $previewData['emergency_contact_number'] }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="text-sm font-medium text-gray-500">Address</span>
                                        <p class="text-gray-900">{{ $previewData['address'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('tenants.store') }}">
                                @csrf
                                @foreach ($previewData as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="window.history.back()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Edit Information
                                    </button>

                                    <button type="submit" name="action" value="save_only"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Create Tenant Only
                                    </button>

                                    <button type="submit" name="create_lease_next" value="1"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        <i class="fa-solid fa-arrow-right mr-2"></i> Create Tenant & Lease
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="mb-16 w-full px-4">
    <div class="grid">
        <div class="col-start-1 row-start-1 self-start pt-1.5 px-[12.5%]" aria-hidden="true">
            <div class="h-1 w-full bg-gray-200 rounded">
                <div class="h-full bg-blue-600 transition-all duration-500 rounded"
                    style="width: {{ (($currentStep - 1) / 3) * 100 }}%">
                </div>
            </div>
        </div>

        <div class="col-start-1 row-start-1 flex justify-between">
            @for ($i = 1; $i <= 4; $i++)
                <div class="flex flex-col items-center w-1/4">

                    <div
                        class="z-10 w-3 h-3 rounded-full ring-4 ring-white transition-all duration-300
                        @if ($currentStep >= $i) bg-blue-600 @else bg-gray-300 @endif">
                    </div>

                    <div class="mt-4 text-center">
                        <span
                            class="text-xs font-semibold sm:text-sm block px-2
                            @if ($currentStep >= $i) text-blue-600 @else text-gray-500 @endif">
                            @if ($i == 1)
                                Tenant
                            @elseif($i == 2)
                                Lease
                            @elseif($i == 3)
                                Agreement
                            @else
                                Review & Save
                            @endif
                        </span>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

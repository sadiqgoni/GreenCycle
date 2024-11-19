<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <!-- Greencycle Icon (Optional, adjust as needed) -->
            <div class="flex-1 text-start">
                <svg aria-label="Greencycle Logo" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="h-9 text-green-600 dark:text-green-400">
                    <!-- Example SVG for a recycling logo -->
                    <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8c4.07 0 7.44 3.05 7.93 7h-2.14c-.45-2.28-2.48-4-4.79-4-2.76 0-5 2.24-5 5s2.24 5 5 5c2.31 0 4.23-1.72 4.79-4h2.14c-.49 3.95-3.86 7-7.93 7zm-3-5h2v-2H9v2zm6 0h2v-2h-2v2z"/>
                </svg>
            </div>

            <!-- Display Today's Date -->
            <div class="flex-1 text-center">
                <p class= "text-gray-900 dark:text-white">
                    {{ date('l, F j, Y') }}
                </p>
            </div>

            <!-- Display System Name -->
            <div class="flex-1 text-end">
                <p class="text-lg text-green-700 dark:text-green-300 font-bold uppercase">
                    GreenCycle
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

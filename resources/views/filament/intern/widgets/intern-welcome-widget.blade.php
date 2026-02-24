<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-4">
            {{-- User Avatar Placeholder --}}
            <div class="h-16 w-16 rounded-full bg-primary-500 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($intern->name, 0, 1) }}
            </div>
            
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Welcome back, {{ $intern->name }}!
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Intern ID: <span class="font-mono font-bold text-primary-600 dark:text-primary-400">{{ $intern->intern_id }}</span>
                </p>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-200 dark:border-white/10 pt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Your Onboarding Dashboard
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
                Welcome to the Internship Administration and Performance Evaluation System (IAPES). 
                Here you will be able to track your daily logs, view assigned tasks, and monitor your performance evaluations.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

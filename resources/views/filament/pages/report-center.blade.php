<x-filament::page>

<div class="grid grid-cols-3 gap-6 mb-6">

    <x-filament::card>
        <h2>Total Interns</h2>
        <h1 class="text-3xl font-bold">{{ $this->getTotalInterns() }}</h1>
    </x-filament::card>

    <x-filament::card>
        <h2>Completed Interviews</h2>
        <h1 class="text-3xl font-bold">{{ $this->getCompletedInterviews() }}</h1>
    </x-filament::card>

    <x-filament::card>
        <h2>Selected Interns</h2>
        <h1 class="text-3xl font-bold">{{ $this->getSelectedInterns() }}</h1>
    </x-filament::card>

</div>

<div class="grid grid-cols-2 gap-6">

    <x-filament::button wire:click="exportExcel">
        📊 Export Excel
    </x-filament::button>

    <x-filament::button wire:click="finalSelectionReport">
        🏆 Final Selection PDF
    </x-filament::button>

    <x-filament::button wire:click="downloadAllReports">
        📦 Download All Reports ZIP
    </x-filament::button>

</div>

</x-filament::page>

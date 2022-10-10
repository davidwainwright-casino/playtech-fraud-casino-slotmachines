<x-filament::page>
    @if(isset($_GET['slug']))
    @livewire('fila-game-frame')
    @else
    @livewire('fila-gameoverview')
    @endif
</x-filament::page>
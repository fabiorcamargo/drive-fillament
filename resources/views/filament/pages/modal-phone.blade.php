<div>
    @if ($showModal)
        <x-filament::modal id="modal" width="sm" visible="true">
            <x-slot name="heading">
                Condição atendida!
            </x-slot>

            <p>Este modal é exibido porque a condição foi atendida.</p>

            <x-slot name="footer">
                <x-filament::button wire:click="$set('showModal', false)">
                    Fechar
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endif
</div>

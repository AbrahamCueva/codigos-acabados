
<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}
        <br>
        <div class="flex flex-wrap items-center justify-between gap-3">

            <x-filament::button
                type="button"
                color="gray"
                icon="heroicon-o-arrow-uturn-left"
                wire:click="restoreDefaults"
                wire:confirm="¿Estás seguro de restaurar toda la configuración? Se eliminarán los logotipos personalizados y se recuperarán los valores originales."
            >
                Restaurar valores predeterminados
            </x-filament::button>

            <x-filament::button
                type="submit"
                icon="heroicon-o-check"
            >
                Guardar configuración
            </x-filament::button>

        </div>
    </form>
</x-filament-panels::page>

<x-filament-panels::page>
    {!! QrCode::size(100)->generate($this->getRecord()->name); !!}
    {{ var_dump($this->getRecord()->email) }}
</x-filament-panels::page>

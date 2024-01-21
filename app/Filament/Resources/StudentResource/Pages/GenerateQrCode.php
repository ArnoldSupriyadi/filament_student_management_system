<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class GenerateQrCode extends Page
{
    use InteractsWithRecord;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.generate-qr-code';

    public function mount(int | string $record): void
    {
        // dd($record);
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
    }

}

<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

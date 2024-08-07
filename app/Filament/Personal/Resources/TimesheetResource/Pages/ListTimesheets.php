<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('inWork')
                ->label('Entrar a trabajar')
                ->color('info')
                ->requiresConfirmation()
            ->action(function (){
                $user = Auth::user();
                $timesheet = new Timesheet();

                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->day_out = Carbon::now();
                $timesheet->type = 'work';

                $timesheet->save();
            }),
            Actions\Action::make('inPause')
                ->label('Comenzar Pausa')
                ->color('success')
                ->requiresConfirmation(),
            Actions\CreateAction::make()
                ->label('Nuevo timesheet'),
        ];
    }
}

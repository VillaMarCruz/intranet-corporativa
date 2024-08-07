<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {

        $lastTimesheet = Timesheet::where('user_id',Auth::user()->id)->orderBy('id','desc')->first();
        if($lastTimesheet == null){
            return [
                Action::make('inWork')
                    ->label('Entrar a trabajar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (){
                        $user = Auth::user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                        $timesheet->type = 'work';
                        $timesheet->save();

                        Notification::make()
                            ->title('Has entrado a trabajar')
                            ->body('Has comenzado a trabajar a las:'.Carbon::now())
                            ->color('success')
                            ->success()
                            ->send();
                    }),
                Actions\CreateAction::make(),
            ];
        }

        return [
            Actions\Action::make('inWork')
                ->label('Entrar a trabajar')
                ->color('info')
                ->visible(!$lastTimesheet->day_out == null)
                ->disabled($lastTimesheet->day_out == null)
                ->requiresConfirmation()
            ->action(function (){
                $user = Auth::user();
                $timesheet = new Timesheet();

                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                $timesheet->type = 'work';

                $timesheet->save();

                Notification::make()
                    ->title('Has entrado a trabajar')
                    ->body('Has comenzado a trabajar a las:'.Carbon::now())
                    ->color('success')
                    ->success()
                    ->send();
            }),
            Actions\Action::make('stopWork')
                ->label('Parar de trabajar')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type!='pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                    $lastTimesheet->save();
                    Notification::make()
                        ->title('Has parado de trabajar')
                        ->success()
                        ->color('success')
                        ->send();
                }),
            Actions\Action::make('inPause')
                ->label('Comenzar Pausa')
                ->color('success')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type!='pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
            ->action(function () use ($lastTimesheet) {
                $lastTimesheet->day_out = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                $lastTimesheet->save();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = Auth::user()->id;
                $timesheet->day_in = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                $timesheet->type = 'pause';
                $timesheet->save();

                Notification::make()
                    ->title('Comienzas tu pausa')
                    ->color('info')
                    ->info()
                    ->send();
            }),
            Actions\Action::make('stopPause')
                ->label('Parar Pausa')
                ->color('success')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type=='pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now(new \DateTimeZone('America/Guayaquil'));
                    $timesheet->type = 'work';
                    $timesheet->save();

                    Notification::make()
                        ->title('Comienzas de nuevo a trabajar')
                        ->color('info')
                        ->info()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Nuevo timesheet'),
        ];
    }
}

<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Holidays', $this->getPendingHoliday(Auth::user())),
            Stat::make('Approved Holidays', $this->getApprovedHoliday(Auth::user())),
            Stat::make('Total Works', $this->getTotalWork(Auth::user())),
        ];
    }

    protected function getPendingHoliday(User $user): int
    {
        return Holiday::
            where('user_id', $user->id)
            ->where('type', 'pending')->count();
    }

    protected function getApprovedHoliday(User $user): int
    {
        return Holiday::
        where('user_id', $user->id)
            ->where('type', 'approved')->count();
    }

    protected function getTotalWork(User $user)
    {

        $timesheets = Timesheet::where('user_id', $user->id)->where('type', 'work')->get();
        $sumHours = 0;
        foreach ($timesheets as $timesheet){
            $startTime = Carbon::parse($timesheet->day_in);
            $endTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $endTime->diffInSeconds($startTime);

            $sumHours = $sumHours + $totalDuration;
        }
        $tiempoCarbon = gmdate('H:i:s', $sumHours);
        return $tiempoCarbon;
    }
}

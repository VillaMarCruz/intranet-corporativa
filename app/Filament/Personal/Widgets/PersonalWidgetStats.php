<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use DateTimeZone;
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
            Stat::make('Total Pause', $this->getTotalPause(Auth::user())),
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
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type','work')->whereDate('created_at', Carbon::today())->get();
        $totalSeconds = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in, new \DateTimeZone('America/Guayaquil'));
            $finishTime = Carbon::parse($timesheet->day_out, new \DateTimeZone('America/Guayaquil'));

            if ($finishTime > $startTime) {
                $totalDuration = $startTime->diffInSeconds($finishTime);
                $totalSeconds += $totalDuration;
            }
        }

        return gmdate("H:i:s", $totalSeconds);
    }

    protected function getTotalPause(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type','pause')->whereDate('created_at', Carbon::today())->get();
        $totalSeconds = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in, new \DateTimeZone('America/Guayaquil'));
            $finishTime = Carbon::parse($timesheet->day_out, new \DateTimeZone('America/Guayaquil'));

            if ($finishTime > $startTime) {
                $totalDuration = $startTime->diffInSeconds($finishTime);
                $totalSeconds += $totalDuration;
            }
        }

        return gmdate("H:i:s", $totalSeconds);
    }
}

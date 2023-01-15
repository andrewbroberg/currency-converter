<?php

namespace App\Policies;

use App\Models\HistoricalRateReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HistoricalRateReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, HistoricalRateReport $historicalRateReport): bool
    {
        return $historicalRateReport->user->is($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, HistoricalRateReport $historicalRateReport): bool
    {
        return $historicalRateReport->user->is($user);
    }

    public function delete(User $user, HistoricalRateReport $historicalRateReport): bool
    {
        return $historicalRateReport->user->is($user);
    }

    public function restore(User $user, HistoricalRateReport $historicalRateReport): bool
    {
        return $historicalRateReport->user->is($user);
    }


    public function forceDelete(User $user, HistoricalRateReport $historicalRateReport): bool
    {
        return $historicalRateReport->user->is($user);
    }
}

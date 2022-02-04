<?php

namespace App\Services\Dashboard;



use Illuminate\Database\Eloquent\Collection;

interface IDashboardService
{
    public function dashboard(string $userID);
    public function getDashboard2022(): Collection;
    public function getTransactionCountDaily(): Collection;
    public function getTransactionCountMonthly(): Collection;
    public function getTransactionCountWeekly(): Collection;
}

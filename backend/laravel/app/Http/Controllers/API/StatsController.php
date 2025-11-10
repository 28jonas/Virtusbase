<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function incomeExpenseStats()
    {
        $userId = request()->user['id'];

        $totalIncome = Income::whereHas('card', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalExpenses = Expense::whereHas('card', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return response()->json([
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_balance' => $totalIncome - $totalExpenses
        ]);
    }
    // public function incomeExpenseStats()
    // {
    //     $userId = request()->user['id'];
    //     $now = Carbon::now();
    //     $currentMonth = $now->month;
    //     $currentYear = $now->year;
    //     $currentDay = $now->day;

    //     // Datum van vorige maand (zelfde dag)
    //     $lastMonth = Carbon::now()->subMonth();
    //     $lastMonthDay = min($currentDay, $lastMonth->daysInMonth);
    //     $previousMonthDate = Carbon::create($lastMonth->year, $lastMonth->month, $lastMonthDay);

    //     // Totale inkomsten voor huidige maand
    //     $totalIncome = Income::where('user_id', $userId)
    //         ->whereMonth('created_at', $currentMonth)
    //         ->whereYear('created_at', $currentYear)
    //         ->sum('amount');

    //     // Totale uitgaven voor huidige maand
    //     $totalExpense = Expense::where('profile_id', $userId)
    //         ->whereMonth('created_at', $currentMonth)
    //         ->whereYear('created_at', $currentYear)
    //         ->sum('amount');

    //     // Inkomsten tot dezelfde dag vorige maand
    //     $previousMonthIncome = Income::where('profile_id', $userId)
    //         ->whereMonth('created_at', $lastMonth->month)
    //         ->whereYear('created_at', $lastMonth->year)
    //         ->whereDate('created_at', '<=', $previousMonthDate)
    //         ->sum('amount');

    //     // Uitgaven tot dezelfde dag vorige maand
    //     $previousMonthExpense = Expense::where('profile_id', $userId)
    //         ->whereMonth('created_at', $lastMonth->month)
    //         ->whereYear('created_at', $lastMonth->year)
    //         ->whereDate('created_at', '<=', $previousMonthDate)
    //         ->sum('amount');

    //     // Bereken percentages
    //     $incomePercentage = $previousMonthIncome > 0
    //         ? round((($totalIncome - $previousMonthIncome) / $previousMonthIncome) * 100, 2)
    //         : 0;

    //     $expensePercentage = $previousMonthExpense > 0
    //         ? round((($totalExpense - $previousMonthExpense) / $previousMonthExpense) * 100, 2)
    //         : 0;

    //     return response()->json([
    //         'totalIncome' => $totalIncome,
    //         'totalExpense' => $totalExpense,
    //         'incomePercentage' => $incomePercentage,
    //         'expensePercentage' => $expensePercentage,
    //     ]);
    // }
}
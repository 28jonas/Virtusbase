<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends BaseApiController
{
    public function index()
    {
        Log::info('Get transactions request received');
        $userId = request()->user['id'];
        
        // Get expenses for the current user via card relationship
        $expenses = Expense::whereHas('card', function($query) use ($userId) {
                $query->where('profile_id', $userId);
            })
            ->with(['categories', 'card.bank'])
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'type' => 'expense',
                    'amount' => $expense->amount,
                    'description' => $expense->description,
                    'date' => $expense->date,
                    'category' => $expense->categories->first()?->name ?? 'Uncategorized',
                    'card' => $expense->card->last_four ?? 'No card',
                    'created_at' => $expense->created_at,
                ];
            });

        // Get incomes for the current user via card relationship
        $incomes = Income::whereHas('card', function($query) use ($userId) {
                $query->where('profile_id', $userId);
            })
            ->with(['categories', 'card.bank'])
            ->get()
            ->map(function ($income) {
                return [
                    'id' => $income->id,
                    'type' => 'income',
                    'amount' => $income->amount,
                    'description' => $income->description,
                    'date' => $income->date,
                    'category' => $income->categories->first()?->name ?? 'Uncategorized',
                    'card' => $income->card->last_four ?? 'No card',
                    'created_at' => $income->created_at,
                ];
            });

        // Combine and sort transactions
        $transactions = $expenses->concat($incomes)
            ->sortByDesc('created_at')
            ->values()
            ->all();

        return response()->json($transactions);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Card;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];

        $expenses = Expense::whereHas('card', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })
            ->with(['card', 'categories'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        Log::info('Store expense request data:', $request->all());
        $userId = request()->user['id'];

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'card_id' => 'nullable|exists:bankingcards,id', // Let op: card_id i.p.v. bankingcard_id
        ]);

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'bankingcard_id' => $request->card_id, // Gebruik bankingcard_id i.p.v. user_id
            ]);

            // Attach the category (polymorphic many-to-many)
            $expense->categories()->attach($request->category_id);

            // Update card balance if a card was selected
            if ($request->card_id) {
                // Gebruik het Card model direct in plaats van via user
                $card = Card::where('id', $request->card_id)
                    ->where('profile_id', $userId) // Zorg dat de card van de juiste user is
                    ->firstOrFail();

                $card->balance = $card->balance - $request->amount; // Aftrekken voor expense
                $card->save();
            }

            DB::commit();

            return response()->json($expense, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating expense: ' . $e->getMessage());
            return response()->json(['error' => 'Er is iets misgegaan bij het toevoegen van de uitgave.'], 500);
        }
    }
}
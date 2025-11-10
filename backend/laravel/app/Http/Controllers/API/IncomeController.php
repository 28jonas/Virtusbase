<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Card;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index()
    {
        $userId = request()->user['id'];

        $incomes = Income::whereHas('card', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })
            ->with(['card', 'categories'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($incomes);
    }

    public function store(Request $request)
    {
        Log::info('Store income request data:', $request->all());
        $userId = request()->user['id'];
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'card_id' => 'nullable|exists:bankingcards,id',
        ]);

        DB::beginTransaction();
        try {
            $income = Income::create([
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'bankingcard_id' => $request->card_id,
                //'category_id' => $request->category_id
            ]);

            // Attach the category
            $income->categories()->attach($request->category_id);

            // Update card balance if a card was selected
            if ($request->card_id) {
                // Gebruik het Card model direct in plaats van via user
                $card = Card::where('id', $request->card_id)
                    ->where('profile_id', $userId) // Zorg dat de card van de juiste user is
                    ->firstOrFail();

                $card->balance = $card->balance + $request->amount;
                $card->save();
            }

            DB::commit();

            return response()->json($income, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating income: ' . $e->getMessage());
            return response()->json(['error' => 'Er is iets misgegaan bij het toevoegen van de inkomst.'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Card;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoalController extends BaseApiController
{
    public function index()
{
    $userId = request()->user['id'];

    $goals = Goal::whereHas('card', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })
        ->with(['card.bank', 'categories']) // Voeg .bank toe aan card
        ->orderBy('date', 'asc')
        ->get();
    
    Log::info('goals', [$goals]);
    return response()->json($goals);
}

    public function store(Request $request)
    {
        $userId = request()->user['id'];
        $request->validate([
            'type' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'goalDate' => 'required|date|after_or_equal:today',
            'cardId' => 'nullable|exists:bankingcards,id',
            'transfer' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update card balance if a card was selected and transfer is made
            // if ($request->cardId && $request->transfer > 0) {
            //     Log::info('Updating card balance for card ID: ' . $request->cardId . ' with transfer amount: ' . $request->transfer);
            //     $card = Card::findOrFail($request->cardId);
            //     Log::info('Found card: ' . json_encode($card));
            //     // Check if user owns the card
            //     if ($card->user_id !== $userId) {
            //         return response()->json(['error' => 'Unauthorized'], 403);
            //     }

            //     // Validate transfer amount
            //     if ($request->transfer > $card->balance) {
            //         return response()->json(['error' => 'Transfer amount exceeds card balance'], 422);
            //     }

            //     $card->balance = $card->balance - $request->transfer;
            //     $card->save();
            //     Log::info('Updated card balance for card ID: ' . $request->cardId . ' with transfer amount: ' . $request->transfer);
            // }
            Log::info('Create goal: ' . json_encode($request->all()));

            $goal = Goal::create([
                'amount' => $request->amount,
                'date' => $request->goalDate,
                'bankingcard_id' => $request->cardId ?: null,
                'transfer' => $request->transfer ?: 0,
                'user_id' => $userId,
            ]);

            // Attach the category
            $goal->categories()->attach($request->type);
            Log::info('Attached category ID ' . $request->type . ' to goal ID ' . $goal->id);
            DB::commit();

            return response()->json($goal, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating goal: ' . $e->getMessage());
            return response()->json(['error' => 'Er is iets misgegaan bij het toevoegen van het doel.'], 500);
        }
    }

    public function update(Request $request, Goal $goal)
    {
        // Check if user owns the goal
        if ($goal->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'type' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'goalDate' => 'required|date',
            'cardId' => 'nullable|exists:bankingcards,id',
            'transfer' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Handle card balance updates if card changed or transfer amount changed
            if ($request->cardId && $request->transfer > 0) {
                $card = Card::findOrFail($request->cardId);

                if ($card->user_id !== auth()->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                // Calculate transfer difference
                $transferDifference = $request->transfer - $goal->transfer;

                if ($transferDifference > $card->balance) {
                    return response()->json(['error' => 'Transfer amount exceeds card balance'], 422);
                }

                $card->balance = $card->balance - $transferDifference;
                $card->save();
            }

            $goal->update([
                'amount' => $request->amount,
                'date' => $request->goalDate,
                'card_id' => $request->cardId ?: null,
                'transfer' => $request->transfer ?: 0,
            ]);

            // Sync categories
            $goal->categories()->sync([$request->type]);

            DB::commit();

            return response()->json($goal);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Er is iets misgegaan bij het bijwerken van het doel.'], 500);
        }
    }
}
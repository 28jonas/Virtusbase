<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FoodItemController extends BaseApiController
{
    public function index(Request $request)
    {
        Log::info("request userid: " . json_encode($request->user['id']));
        $query = FoodItem::query()
            ->where(function ($query) use ($request) {
                $query->where('profile_id', $request->user['id']);

                if ($request->boolean('showPublic', true)) {
                    $query->orWhere('is_public', true);
                }
            });

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $foodItems = $query->orderBy('name')->paginate(10);

        return response()->json($foodItems);
    }

    public function store(Request $request)
    {
        Log::info('Store food item request data:', $request->all());
        $userId = request()->user['id'];
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'servingSize' => 'required|numeric|min:0.01',
            'servingUnit' => 'required|string|max:50',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'sugar' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'isPublic' => 'boolean',
        ]);
        

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
        }

        $foodItem = FoodItem::create([
            'profile_id' => $userId,
            'name' => $validated['name'],
            'image_path' => $imagePath,
            'serving_size' => $validated['servingSize'],
            'serving_unit' => $validated['servingUnit'],
            'calories' => $validated['calories'],
            'protein' => $validated['protein'],
            'fat' => $validated['fat'],
            'carbs' => $validated['carbs'],
            'fiber' => $validated['fiber'] ?? 0,
            'sugar' => $validated['sugar'] ?? 0,
            'sodium' => $validated['sodium'] ?? 0,
            'is_public' => $validated['isPublic'] ?? false,
        ]);

        return response()->json([
            'message' => 'Food item created successfully',
            'foodItem' => $foodItem
        ]);
    }

    public function search(Request $request)
{
    $userId = $request->user['id'];
    
    $query = FoodItem::query()
        ->where(function ($query) use ($userId) {
            // User's own items OR public items OR global items
            $query->where('profile_id', $userId)
                  ->orWhere(function ($q) {
                      $q->where('is_public', true)
                        ->orWhereNull('profile_id');
                  });
        });

    if ($request->has('search') && !empty($request->search)) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $foodItems = $query->orderBy('name')->get();

    return response()->json($foodItems);
}

    public function destroy(FoodItem $foodItem)
    {
        if ($foodItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($foodItem->image_path) {
            Storage::disk('public')->delete($foodItem->image_path);
        }

        $foodItem->delete();

        return response()->json(['message' => 'Food item deleted successfully']);
    }
}
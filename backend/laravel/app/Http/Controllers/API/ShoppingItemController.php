<?php

namespace App\Http\Controllers\API;

use App\Models\ShoppingItem;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;

class ShoppingItemController extends BaseApiController
{
    public function index(ShoppingList $shoppingList)
    {
        //$this->authorize('view', $shoppingList);
        $items = $shoppingList->items()
        ->with(['addedBy' => function($query) {
            $query->select('id', 'first_name'); // Alleen nodig velden ophalen
        }])
        ->get();

        //$items = $shoppingList->items()->with('addedBy')->get();
        return $this->sendResponse($items, 'Shopping items retrieved successfully.');
    }

    public function store(Request $request, ShoppingList $shoppingList)
    {
        //$this->authorize('update', $shoppingList);
        Log::info('[ShoppingItemController@store] Creating shopping item with data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        $userId = request()->user['id'];

        $item = $shoppingList->items()->create([
            'name' => $request->name,
            'quantity' => $request->quantity ?? 1,
            'added_by_profile_id' => $userId
        ]);

        return $this->sendResponse($item, 'Item added successfully.', 201);
    }

    public function update(Request $request, ShoppingItem $shoppingItem)
    {
        //$this->authorize('update', $shoppingItem->list);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'quantity' => 'integer|min:1',
            'is_completed' => 'boolean'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $data = $request->all();
        if ($request->has('is_completed') && $request->is_completed) {
            $data['completed_at'] = now();
        } elseif ($request->has('is_completed') && !$request->is_completed) {
            $data['completed_at'] = null;
        }

        $shoppingItem->update($data);

        return $this->sendResponse($shoppingItem, 'Item updated successfully.');
    }

    public function destroy(ShoppingItem $shoppingItem)
    {
        Log::info('[ShoppingItemController@destroy] Deleting item:', $shoppingItem->toArray());
        //$this->authorize('update', $shoppingItem->list);
        $shoppingItem->delete();

        return $this->sendResponse(null, 'Item deleted successfully.');
    }

    public function toggleComplete(ShoppingItem $shoppingItem)
    {
        //$this->authorize('update', $shoppingItem->list);
        Log::info('[ShoppingItemController@toggleComplete] Toggling completion status for item:', $shoppingItem->toArray());
        $shoppingItem->update([
            'is_completed' => !$shoppingItem->is_completed,
            'completed_at' => $shoppingItem->is_completed ? null : now()
        ]);

        return $this->sendResponse($shoppingItem, 'Item status updated successfully.');
    }
}
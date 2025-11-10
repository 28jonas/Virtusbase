<?php

namespace App\Http\Controllers\API;

use App\Models\ShoppingList;
use App\Models\Family;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;

class ShoppingListController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];

        // Haal de families op waar de gebruiker lid van is
        $userFamilies = Family::whereHas('members', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->pluck('id');

        $lists = ShoppingList::where(function ($query) use ($userId, $userFamilies) {
            // Persoonlijke shopping lists
            $query->where(function ($q) use ($userId) {
                $q->where('owner_type', Profile::class)
                    ->where('owner_id', $userId);
            });

            // Gezins shopping lists - alleen toevoegen als gebruiker bij families hoort
            if ($userFamilies->isNotEmpty()) {
                $query->orWhere(function ($q) use ($userFamilies) {
                    $q->where('owner_type', Family::class)
                        ->whereIn('owner_id', $userFamilies);
                });
            }

            // Gedeelde shopping lists
            $query->orWhereHas('sharedWithUsers', function ($q) use ($userId) {
                $q->where('profiles.id', $userId);
            });
        })
            ->with([
                'items',
                'sharedWithUsers:id,email', // Gebruik email voor gedeelde gebruikers
            ])
            ->withCount(['items'])
            ->get();

        // Eager load owners op de juiste manier
        $profileOwners = $lists->where('owner_type', Profile::class)
            ->pluck('owner_id')
            ->unique()
            ->toArray();

        $familyOwners = $lists->where('owner_type', Family::class)
            ->pluck('owner_id')
            ->unique()
            ->toArray();

        $profiles = Profile::whereIn('id', $profileOwners)
            ->select('id', 'email')
            ->get()
            ->keyBy('id');

        $families = Family::whereIn('id', $familyOwners)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        // Voeg owner details handmatig toe
        $lists->each(function ($list) use ($profiles, $families) {
            if ($list->owner_type === Profile::class && isset($profiles[$list->owner_id])) {
                $list->owner = $profiles[$list->owner_id];
            } elseif ($list->owner_type === Family::class && isset($families[$list->owner_id])) {
                $list->owner = $families[$list->owner_id];
            }
        });

        return $this->sendResponse($lists, 'Shopping lists retrieved successfully.');
    }

    public function store(Request $request)
    {
        Log::info('[ShoppingListController@store] Creating shopping list with data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'owner_type' => 'required|in:user,family',
            'family_id' => 'nullable|required_if:owner_type,family|exists:families,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->owner_type === 'family') {
            $family = Family::find($request->family_id);
            // if (!auth()->user()->families->contains($family->id)) {
            //     return $this->sendError('Unauthorized access to family.', [], 403);
            // }
            $data['owner_type'] = Family::class;
            $data['owner_id'] = $family->id;
        } else {
            $data['owner_type'] = Profile::class;
            $userId = request()->user['id'];
            $data['owner_id'] = $userId;
        }

        $shoppingList = ShoppingList::create($data);

        return $this->sendResponse($shoppingList, 'Shopping list created successfully.', 201);
    }

    public function show(ShoppingList $shoppingList)
    {
        $this->authorize('view', $shoppingList);
        $shoppingList->load(['items', 'sharedWithUsers']);

        return $this->sendResponse($shoppingList, 'Shopping list retrieved successfully.');
    }

    public function update(Request $request, ShoppingList $shoppingList)
    {
        //$this->authorize('update', $shoppingList);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $shoppingList->update($request->all());

        return $this->sendResponse($shoppingList, 'Shopping list updated successfully.');
    }

    public function destroy(ShoppingList $shoppingList)
    {
        //$this->authorize('delete', $shoppingList);
        $shoppingList->delete();

        return $this->sendResponse(null, 'Shopping list deleted successfully.');
    }

    public function share(Request $request, ShoppingList $shoppingList)
    {
        $this->authorize('update', $shoppingList);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'permission_level' => 'required|in:view,edit'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        // Voeg sharing toe
        $shoppingList->sharedWithUsers()->attach($user->id, [
            'permission_level' => $request->permission_level
        ]);

        $shoppingList->update(['is_shared' => true]);

        return $this->sendResponse(null, 'Shopping list shared successfully.');
    }

    public function unshare(ShoppingList $shoppingList, User $user)
    {
        $this->authorize('update', $shoppingList);

        $shoppingList->sharedWithUsers()->detach($user->id);

        // Zet is_shared op false als er geen gedeelde gebruikers meer zijn
        if ($shoppingList->sharedWithUsers()->count() === 0) {
            $shoppingList->update(['is_shared' => false]);
        }

        return $this->sendResponse(null, 'User removed from shopping list.');
    }
}
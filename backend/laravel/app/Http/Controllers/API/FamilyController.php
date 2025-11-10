<?php

namespace App\Http\Controllers\API;

use App\Models\Family;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FamilyController extends BaseApiController
{
    public function index()
    {
        Log::info('[FamilyController@index] JWT payload user:', [
            'user' => request()->user
        ]);

        // Gebruik de user ID uit JWT i.p.v. database user
        $userId = request()->user['id'];

        // Vervang auth()->user() door:
        $families = Family::whereHas('members', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->with('members')->get();


        return $this->sendResponse($families, 'Families retrieved successfully.');
    }

    public function store(Request $request)
    {
        Log::info('[FamilyController@store] Creating family with data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $family = Family::create(['name' => $request->name]);

        // Gebruik de user informatie die door je middleware is toegevoegd
        $userId = $request->user['id'];

        // Zoek het profile op basis van user_id
        $profile = Profile::where('id', $userId)->first();

        if ($profile) {
            $profile->families()->attach($family->id, ['role' => 'owner']);
        } else {
            // Fallback: voeg direct toe aan pivot table
            DB::table('family_profile')->insert([
                'family_id' => $family->id,
                'profile_id' => $userId,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Laad de members relatie
        $family->load('members');

        return $this->sendResponse($family, 'Family created successfully.', 201);
    }


    // public function show(Family $family)
    // {
    //     // Authorisatie: is user lid van dit gezin?
    //     if (!auth()->user()->families->contains($family->id)) {
    //         return $this->sendError('Unauthorized access to family.', [], 403);
    //     }

    //     $family->load('members');
    //     return $this->sendResponse($family, 'Family retrieved successfully.');
    // }
    public function show(Family $family)
    {
        Log::info('[FamilyController@show] Requested family:', [
            'family_id' => $family->id,
            'jwt_user' => request()->user // Log de JWT user data
        ]);

        // Gebruik de user ID uit JWT (aangenomen dat je middleware user info toevoegd)
        $userId = request()->user['id'];

        // Authorisatie: is user lid van dit gezin?
        // Vervang auth()->user()->families->contains() door:
        $isMember = $family->members()->where('profile_id', $userId)->exists();

        if (!$isMember) {
            Log::warning('[FamilyController@show] Unauthorized access attempt', [
                'user_id' => $userId,
                'family_id' => $family->id
            ]);
            return $this->sendError('Unauthorized access to family.', [], 403);
        }

        // Laad de members met eventuele extra relaties
        $family->load('members');

        Log::info('[FamilyController@show] Family loaded successfully', [
            'family' => $family
        ]);

        return $this->sendResponse($family, 'Family retrieved successfully.');
    }

    public function update(Request $request, Family $family)
    {
        $userId = $request->user['id'];
        // Authorisatie: is user eigenaar?
        if ($userId->getRoleInFamily($family) !== 'owner') {
            return $this->sendError('Only owners can update the family.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $family->update($request->all());

        return $this->sendResponse($family, 'Family updated successfully.');
    }

    public function destroy(Family $family)
    {
        // Authorisatie: is user eigenaar?
        if (auth()->user()->getRoleInFamily($family) !== 'owner') {
            return $this->sendError('Only owners can delete the family.', [], 403);
        }

        $family->delete();

        return $this->sendResponse(null, 'Family deleted successfully.');
    }

    public function addMember(Request $request, Family $family)
    {
        $userId = $request->user['id'];
        $requestingProfile = Profile::find($userId);
        // Authorisatie: heeft user rechten om leden toe te voegen?
        if (!$requestingProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Requesting user not found.'
            ], 404);
        }

        if (!$requestingProfile->hasPermissionInFamily($family, 'invite_members')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add members.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:profiles,email',
            'role' => 'required|in:parent,child,guest'
        ]);



        if ($validator->fails()) {
            Log::error('[FamilyController@addMember] Validation Error:', $validator->errors()->all());
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $profile = Profile::where('email', $request->email)->first();

        //Controleer of member al in het gezin zit
        if ($family->members()->where('profile_id', $profile->id)->exists()) {
            return $this->sendError('This user is already a member of this family.', [], 409);
        }
        Log::info('[FamilyController@addMember] Adding member:', [
            'request'=> $request->all(),
        ]);
        // Voeg gebruiker toe aan gezin
        $family->members()->attach($profile->id, ['role' => 1]);

        return $this->sendResponse(null, 'Member added successfully.');
    }

    public function removeMember(Family $family, Profile $member)
    {
        // Authorisatie: heeft user rechten om leden te verwijderen?
        if (!auth()->user()->hasPermissionInFamily($family, 'remove_members')) {
            return $this->sendError('Unauthorized to remove members.', [], 403);
        }

        $family->members()->detach($member->id);

        return $this->sendResponse(null, 'Member removed successfully.');
    }
}
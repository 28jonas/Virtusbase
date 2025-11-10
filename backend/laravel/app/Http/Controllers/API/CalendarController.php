<?php

namespace App\Http\Controllers\API;

use App\Models\Calendar;
use App\Models\Family;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CalendarController extends BaseApiController
{
    use AuthorizesRequests;
    // public function index()
    // {

    //     $userId = request()->user['id'];

    //     $calendars = Calendar::where(function ($query) use ($userId) {
    //         // Persoonlijke kalenders (user type)
    //         $query->where(function ($q) use ($userId) {
    //             $q->where('owner_type', Profile::class)
    //                 ->where('owner_id', $userId);
    //         })
    //             // Gezinskalenders waar user lid van is (alleen family types)
    //             ->orWhere(function ($q) use ($userId) {
    //                 $q->where('owner_type', Family::class) // alleen families!
    //                     ->whereHasMorph(
    //                         'owner',
    //                         [Family::class],
    //                         function ($q) use ($userId) {
    //                             $q->whereHas('members', function ($q) use ($userId) {
    //                                 $q->where('profile_id', $userId);
    //                             });
    //                         }
    //                     );
    //             });
    //     })->with('events')->get();

    //     Log::info('aantal calendars:', ['count' => $calendars->count()]);
    //     Log::info('calendars data:', $calendars->toArray());

    //     return $this->sendResponse($calendars, 'Calendars retrieved successfully.');
    // }

    public function index()
    {
        $userId = request()->user['id'];

        // Haal de families op waar de gebruiker lid van is
        $userFamilies = Family::whereHas('members', function ($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->pluck('id');

        $calendars = Calendar::where(function ($query) use ($userId, $userFamilies) {
            // Persoonlijke kalenders
            $query->where(function ($q) use ($userId) {
                $q->where('owner_type', Profile::class)
                    ->where('owner_id', $userId);
            });

            // Gezinskalenders - alleen toevoegen als gebruiker bij families hoort
            if ($userFamilies->isNotEmpty()) {
                $query->orWhere(function ($q) use ($userFamilies) {
                    $q->where('owner_type', Family::class)
                        ->whereIn('owner_id', $userFamilies);
                });
            }

            // Gedeelde kalenders (als je sharing functionaliteit hebt)
            // $query->orWhereHas('sharedWithUsers', function ($q) use ($userId) {
            //     $q->where('profiles.id', $userId);
            // });
        })->with('events')->get();

        Log::info('aantal calendars:', ['count' => $calendars->count()]);
        Log::info('calendars data:', $calendars->toArray());

        return $this->sendResponse($calendars, 'Calendars retrieved successfully.');
    }

    public function store(Request $request)
    {
        Log::info('[CalendarController@store] Creating calendar with data:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'owner_type' => 'required|in:user,family',
            'family_id' => 'nullable|required_if:owner_type,family|exists:families,id',
            'color' => 'string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            Log::error('Validation Error.', $validator->errors()->toArray());
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $data = $request->all();

        // Bepaal eigenaar op basis van type
        if ($request->owner_type === 'family') {
            $family = Family::find($request->family_id);
            // Authorisatie: is user lid van dit gezin?
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

        $calendar = Calendar::create($data);

        return $this->sendResponse($calendar, 'Calendar created successfully.', 201);
    }

    public function show(Calendar $calendar)
    {
        // Authorisatie: heeft gebruiker toegang tot deze kalender?
        $this->authorize('view', $calendar);

        $calendar->load('events');
        return $this->sendResponse($calendar, 'Calendar retrieved successfully.');
    }

    public function update(Request $request, Calendar $calendar)
    {
        $this->authorize('update', $calendar);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'color' => 'string',
            'is_public' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $calendar->update($request->all());

        return $this->sendResponse($calendar, 'Calendar updated successfully.');
    }

    public function destroy(Calendar $calendar)
    {
        $this->authorize('delete', $calendar);

        $calendar->delete();

        return $this->sendResponse(null, 'Calendar deleted successfully.');
    }
}
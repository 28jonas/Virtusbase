<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityTrackingController extends Controller
{
    public function index(Request $request)
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $currentWeekStart->copy()->addDays($i)->day;
        }

        $weekEnd = $currentWeekStart->copy()->addDays(6);
        $weekLabel = $currentWeekStart->format('d') . ' - ' . $weekEnd->format('d') . ' ' . $weekEnd->format('M Y');

        // Example activity data - replace with your actual data source
        $activityData = [20, 45, 30, 80, 65, 45, 10];

        return response()->json([
            'selectedDate' => Carbon::now()->day,
            'dates' => $dates,
            'activityData' => $activityData,
            'currentWeekStart' => $currentWeekStart->toISOString(),
            'weekLabel' => $weekLabel,
        ]);
    }

    public function selectDate(Request $request)
    {
        $request->validate([
            'date' => 'required|integer',
            'currentWeekStart' => 'required|date',
        ]);

        $date = $request->input('date');
        $currentWeekStart = Carbon::parse($request->input('currentWeekStart'));
        
        // Find the full date corresponding to the given day in current week
        $selectedFullDate = null;
        for ($i = 0; $i < 7; $i++) {
            $day = $currentWeekStart->copy()->addDays($i)->day;
            if ($day == $date) {
                $selectedFullDate = $currentWeekStart->copy()->addDays($i);
                break;
            }
        }

        if (!$selectedFullDate) {
            $selectedFullDate = Carbon::now();
        }

        // Simulate different data for different dates
        $dateKey = $selectedFullDate->format('Y-m-d');
        $dataMap = [
            Carbon::now()->format('Y-m-d') => [20, 45, 30, 80, 65, 45, 10],
            Carbon::now()->subDay()->format('Y-m-d') => [30, 60, 40, 50, 75, 35, 20],
            Carbon::now()->addDay()->format('Y-m-d') => [10, 25, 50, 70, 45, 30, 15],
        ];

        $activityData = $dataMap[$dateKey] ?? array_map(function() {
            return rand(10, 90);
        }, range(1, 7));

        return response()->json([
            'activityData' => $activityData,
            'selectedDate' => $date,
            'selectedFullDate' => $selectedFullDate->format('Y-m-d'),
        ]);
    }

    public function previousWeek(Request $request)
    {
        $request->validate([
            'currentWeekStart' => 'required|date',
            'selectedDate' => 'required|integer',
        ]);

        $currentWeekStart = Carbon::parse($request->input('currentWeekStart'))->subWeek();
        return $this->generateWeekResponse($currentWeekStart, $request->input('selectedDate'));
    }

    public function nextWeek(Request $request)
    {
        $request->validate([
            'currentWeekStart' => 'required|date',
            'selectedDate' => 'required|integer',
        ]);

        $currentWeekStart = Carbon::parse($request->input('currentWeekStart'))->addWeek();
        return $this->generateWeekResponse($currentWeekStart, $request->input('selectedDate'));
    }

    private function generateWeekResponse($currentWeekStart, $selectedDate)
    {
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $currentWeekStart->copy()->addDays($i)->day;
        }

        $weekEnd = $currentWeekStart->copy()->addDays(6);
        $weekLabel = $currentWeekStart->format('d') . ' - ' . $weekEnd->format('d') . ' ' . $weekEnd->format('M Y');

        // If selected date is not in new dates, select first day
        $newSelectedDate = in_array($selectedDate, $dates) ? $selectedDate : $dates[0];

        return response()->json([
            'dates' => $dates,
            'weekLabel' => $weekLabel,
            'currentWeekStart' => $currentWeekStart->toISOString(),
            'selectedDate' => $newSelectedDate,
        ]);
    }
}
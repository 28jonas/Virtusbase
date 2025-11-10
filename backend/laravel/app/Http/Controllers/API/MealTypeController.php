<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MealType;
use Illuminate\Http\Request;

class MealTypeController extends Controller
{
    public function index()
    {
        $mealTypes = MealType::all();

        return response()->json($mealTypes);
    }
}
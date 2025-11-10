<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Faker\Provider\Base;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}
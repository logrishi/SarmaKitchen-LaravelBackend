<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealCategory;
use Illuminate\Http\Request;

class MealCategoryController extends Controller
{
    public function index()
    {
        $mealCategories = MealCategory::with('products')->get();
        return response()->json($mealCategories);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $image = null;
        if($request->hasFile('image')){
            $ext = $request->file('image')->getClientOriginalExtension();
            $image = $request->image->storeAs('mealCategoryImages', date('mdYHis').random_int(100, 999).'.'.$ext, 'public');
        }
        
        MealCategory::create([
            'meal_category' => ucwords($request->meal_category),
            'image' => $image
        ]);
        return response()->json('Insert Successful!');
    }

    public function show(MealCategory $mealCategory)
    {
        //
    }

    public function edit(MealCategory $mealCategory)
    {
        //
    }

    public function update(Request $request, MealCategory $mealCategory)
    {
        //
    }

    public function destroy(MealCategory $mealCategory)
    {
        //
    }
}
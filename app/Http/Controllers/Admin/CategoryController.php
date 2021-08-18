<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function index()
  {
    $categories = Category::all();
    $subscriptions = [];

    foreach ($categories as $cat) {
      foreach ($cat->subscriptions as $val) {
        if (!in_array($val, $subscriptions)) {
          $subscriptions[] = $val;
        }
      }
    }
    return response()->json([
      'categories' => $categories,
      'subscriptions' => $subscriptions,
    ]);
  }

  public function create()
  {
    return view('categories.create');
  }

  public function store(ValidateCategory $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $category = $validated['category'];
      $name = $validated['meal_type']['name'];
      $img = $validated['meal_type']['image'];
      $ext = $img->extension();

      $image = $img->storeAs(
        'mealTypeImages',
        date('mdYHis') . random_int(100, 999) . '.' . $ext,
        'public'
      );

      $meal_type = [];
      $meal_type['name'] = $name;
      $meal_type['image'] = $image;

      $categories = Category::where('category', $category)->get([
        'category',
        'meal_type',
      ]);
      $existingCategories = $categories->pluck('category')->toArray();
      $existingMealTypes = $categories->pluck('meal_type')->first();

      if (in_array($category, $existingCategories)) {
        $existingMealTypes = collect($existingMealTypes);
        $existingMealTypes = $existingMealTypes->push($meal_type);

        Category::where('category', $category)->update([
          'meal_type' => $existingMealTypes,
        ]);
        return back()->with('status', ' IFInsert Successful!');
      } else {
        Category::create([
          'category' => $category,
          'meal_type' => [$meal_type],
          'dish_type' => isset($validated['dish_type'])
            ? $validated['dish_type']
            : null,
          'subscriptions' => $validated['subscriptions'],
        ]);
        return back()->with('status', 'ELSEInsert Successful!');
      }
    }
  }

  public function show(Category $category)
  {
  }

  public function edit(Category $category)
  {
    //
  }

  public function update(Request $request, Category $category)
  {
    //
  }

  public function destroy(Category $category)
  {
    //
  }
}
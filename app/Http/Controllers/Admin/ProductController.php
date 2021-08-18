<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateProduct;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;

use function GuzzleHttp\Promise\all;

class ProductController extends Controller
{
  public function index()
  {
    $products = Product::all();
    return response()->json($products);
  }

  public function getProductsByMealType(Request $request)
  {
    $meal_type = $request->meal_type;
    $subscription = $request->subscription;

    $products = Product
      // whereJsonContains(
      //   'details->subscriptions',
      //   $subscription
      // )
      // ->
      ::whereJsonContains('meal_type', [
        'name' => $meal_type,
      ])
      ->get();
    return response()->json($products);

    // $products = Product::get('details->customizations as subscriptions');
  }

  public function productSearch(Request $request)
  {
    $term = $request->searchTerm;
    $searchedProduct = Product::where('name', 'LIKE', '%' . $term . '%')->get();
    return response()->json($searchedProduct);
  }

  public function create()
  {
    $categories = Category::all();
    return view('products.create', compact('categories'));
  }

  public function store(ValidateProduct $request)
  {
    // removing null values another way
    // $validated = $this->filterRecursive($validated);
    // public function filterRecursive($values)
    // {
    //   foreach ($values as &$value) {
    //       if (is_array($value)) {
    //           $value = $this->filterRecursive($value);
    //       }
    //   }
    //   return array_filter($values);
    // }

    $validated = $request->validated();
    if ($validated) {
      //filter null values
      foreach ($validated['details'] as $key => $detail) {
        $validated['details'][$key] = array_filter(
          $validated['details'][$key],
          function ($value) {
            return !is_null($value);
          }
        );
      }

      //filter empty arrays
      $validated['details'] = array_filter($validated['details'], function (
        $value
      ) {
        return !empty($value);
      });

      $img = $validated['image'];
      $ext = $img->extension();
      $image = $img->storeAs(
        'productImages',
        date('mdYHis') . random_int(100, 999) . '.' . $ext,
        'public'
      );

      $collection = collect($validated);
      $arrayToSave = $collection->except(['image']);
      $arrayToSave['image'] = $image;
      $arrayToSave = $arrayToSave->toArray();

      Product::create($arrayToSave);
      return back()->with('status', 'Insert Successful!');
    }
  }

  public function show(Product $product)
  {
    //
  }

  public function edit(Product $product)
  {
    //
  }

  public function update(Request $request, Product $product)
  {
    //
  }

  public function destroy(Product $product)
  {
    //
  }
}
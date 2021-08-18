<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subCategory = SubCategory::with('categories')->get();
        return response()->json($subCategory);
    }

    public function getSubCategoriesByCat(Request $request)
    {
          $value = $request->val; 
          $subCategory = SubCategory::whereHas('categories.subCategories', function($query) use($value){
                                    $query->where('category_id', $value);
                                    })->get();

          return response()->json($subCategory);
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
        
        SubCategory::create([
            'sub_category' => ucwords($request->sub_category),
            'image' => $image
        ]);

        //if subcategory image is different table 
        
        // starred line imp so kept here
        //  ** $subCategory->productSubCategoryImages()->create **([  
        //    'image' => $request->image->storeAs('subCategoryImages', date('mdYHis').random_int(100, 999).'.'.$ext, 'public')
        // ]);
        
       return response()->json('Insert Successful');
    }

    public function show(SubCategory $subCategory)
    {
        //
    }

    public function edit(SubCategory $subCategory)
    {
        //
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        //
    }

    public function destroy(SubCategory $subCategory)
    {
        //
    }
}
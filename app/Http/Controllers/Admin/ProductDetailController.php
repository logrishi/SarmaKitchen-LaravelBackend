<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function index()
    {
        //
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
        
        ProductDetail::create([
            'product_id' => $request->product_id,
            'size' => $request->size,
            'note' => $request->note,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $image,
            'is_customizable' => $request->has('for_sale') ? 1 : 0,
            'for_sale'=>$request->has('for_sale') ? 1 : 0
        ]);
       return response()->json('Insert Successful');
    }

    public function show(ProductDetail $productDetail)
    {
        //
    }

    public function edit(ProductDetail $productDetail)
    {
        //
    }

    public function update(Request $request, ProductDetail $productDetail)
    {
        //
    }

    public function destroy(ProductDetail $productDetail)
    {
        //
    }
}
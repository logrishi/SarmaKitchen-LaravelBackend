<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateCart;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $cart = Cart::all();
    return response()->json($cart);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(ValidateCart $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $userId = auth('api')->user()->id;
      $collection = collect($validated);
      $collection = $collection->merge(['user_id' => $userId])->toArray();

      Cart::create($collection);
      return response()->json(['msg' => 'Insert Successful!']);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Cart  $cart
   * @return \Illuminate\Http\Response
   */
  public function show(Cart $cart)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Cart  $cart
   * @return \Illuminate\Http\Response
   */
  public function edit(Cart $cart)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Cart  $cart
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Cart $cart)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Cart  $cart
   * @return \Illuminate\Http\Response
   */
  public function destroy(Cart $cart)
  {
    //
  }
}
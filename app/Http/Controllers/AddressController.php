<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateAddress;
use App\Models\Address;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AddressController extends Controller
{
  public function index()
  {
    $userId = auth('api')->user()->id;
    $address = Address::where('user_id', $userId)
      ->orderBy('id', 'desc')
      ->get();
    $phone = User::where('id', $userId)->get('phone_no');

    return response()->json(['address' => $address, 'phone' => $phone]);
  }

  public function create()
  {
    //
  }

  public function store(ValidateAddress $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $userId = auth('api')->user()->id;

      $collection = collect($validated);
      $addressCollection = $collection->except('phone_no');

      $addressData = $addressCollection
        ->merge(['user_id' => $userId])
        ->toArray();
      $phone = $collection->only('phone_no')->toArray();

      $address = Address::create($addressData);
      User::where('id', $userId)->update($phone);
      // User::where('id', $userId)->update(['phone_no' => $phone]);
      return response()->json([
        'msg' => 'Insert Successful!',
        'address_id' => $address->id,
        'address' => $address->address . ', ' . $address->landmark,
      ]);
    }
  }

  public function show(Address $address)
  {
    //
  }

  public function edit(Address $address)
  {
    //
  }

  public function update(Request $request, Address $address)
  {
    //
  }

  public function destroy($id)
  {
    Address::where('id', $id)->delete();
    return response()->json(["msg" => "Delete Successful"]);
  }
}
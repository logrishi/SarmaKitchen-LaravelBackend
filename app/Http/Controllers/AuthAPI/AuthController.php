<?php

namespace App\Http\Controllers\AuthAPI;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateAddress;
use App\Http\Requests\ValidateLogin;
use App\Http\Requests\ValidateLogout;
use App\Http\Requests\ValidateSignUp;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Carbon\Carbon;
use Exception;
use Throwable;
use App\User;
use Error;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
  public function register(ValidateSignUp $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $user = User::create([
        'email' => $validated['email'],
        'name' => $validated['name'],
        'password' => bcrypt($validated['password']),
      ]);
      $token = JWTAuth::fromUser($user);

      //device token for notificaton
      $deviceToken = DeviceToken::create([
        'user_id' => $user->id,
        'token' => $validated['token'],
      ]);

      return $this->respondWithToken($token, $user, $deviceToken);
    }
  }

  public function login(ValidateLogin $request)
  {
    // $credentials = request(['email', 'password']);
    $validated = $request->validated();

    $collection = collect($validated);
    $credentials = $collection->except(['token'])->toArray();

    // ** $token is jwt token  -- 'token' is device token
    if (!($token = auth('api')->attempt($credentials))) {
      return response()->json(
        ['error' => 'Email Id / Password do not match'],
        401
      );
    }
    // $user = User::where('email', $request->email)->first();
    if ($validated) {
      $user = User::where('email', $validated['email'])->first();

      //device token for notificaton
      $userId = $user->id;
      $dbToken = DeviceToken::where('user_id', $userId)->get('token');
      $inputToken = $validated['token'];

      if (!$dbToken->contains('token', $inputToken)) {
        $deviceToken = DeviceToken::create([
          'user_id' => $user->id,
          'token' => $inputToken,
        ]);
      }
    }
    return $this->respondWithToken(
      $token,
      $user,
      empty($deviceToken) ? $inputToken : $deviceToken->token
    );
  }

  public function refreshToken()
  {
    $token = auth('api')->refresh();
    return response()->json($token);
  }

  protected function respondWithToken($token, $user, $deviceToken)
  {
    return response()->json([
      'auth' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' =>
          auth('api')
            ->factory()
            ->getTTL() * 60,
      ],
      'user' => $user,
      'deviceToken' => $deviceToken,
    ]);
  }

  public function logout(ValidateLogout $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $deviceToken = $validated['token'];
      DeviceToken::where('token', $deviceToken)->delete();
      auth('api')->logout();
      return response()->json(['status' => "Succefully logged out"]);
    }
  }

  public function getAddress()
  {
    $userId = auth('api')->user()->id;
    $address = User::where('id', $userId)
      ->orderBy('id', 'desc')
      ->get('address');
    $phone = User::where('id', $userId)->get('phone_no');

    return response()->json(['address' => $address, 'phone' => $phone]);
  }

  public function saveAddress(ValidateAddress $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $userId = auth('api')->user()->id;
      $storedAddress = User::where('id', $userId)
        ->orderBy('id', 'desc')
        ->get('address');

      $storedAddress = $storedAddress[0]['address'];
      if ($storedAddress == null) {
        $address = User::where('id', $userId)->update([
          'address' => $validated,
        ]);
        return $address;
      } else {
        return $storedAddress;
      }
    }
  }
}
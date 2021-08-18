<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Auth;
use DB;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public $successStatus = 200;
    
    protected function register(Request $request)
    {
         $input = $request->all();
         $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
        ]);
         if ($validator->fails()) { 
                    return response()->json(['error'=>$validator->errors()], 401);   
         }         
                $input['password'] = bcrypt($input['password']); 
                $user = User::create($input); 
                // $success['api_token'] =  $user->createToken('myTokenName')->accessToken;  
                $success['name'] =  $user->name;

                response()->json(['success'=>$success],$this->successStatus); 
                if($success){
                    return $this->generateTokens();               
                }

                // $accessToken = $generateTokens->
                // return  $generateTokens->token_type ;
                // $client = DB::table('oauth_clients')
                //         ->where('password_client', true)
                //         ->first();
                // return response()->json(['success'=>$success],$this->successStatus); 
            
                /// creating tokens on registration--- i am creating on login so not using dis
                
                    // $client = DB::table('oauth_clients')
                        //         ->where('password_client', true)
                        //         ->first();
                        //     $request->request->add([
                        //         'grant_type'    => 'password',
                        //         'client_id'     => $client->id,
                        //         'client_secret' => $client->secret,
                        //         'username' => request('email'),
                        //         'password' => request('password'),
                        //         'scope'         => null,
                        //     ]);
                        // // Fire off the internal request. 
                        // $token = Request::create(
                        //     'oauth/token',
                        //     'POST'
                        // );
                        // return \Route::dispatch($token);

                        //         $newRequest = Request::create('/oauth/token', 'post');
                        //         return Route::dispatch($newRequest)->getContent();
    }
   // this also works
//  public function login(){ 
//      $loginData = 
//         if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
//             $user = Auth::user(); 
//             $success['token'] =  $user->createToken('myToken')->accessToken; 
//             return response()->json(['success' => $success], $this-> successStatus); 
//         } 
//         else{ 
//             return response()->json(['error'=>'Unauthorised'], 401); 
//         } 
//     }
public function login()
{
    if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
     
            $userId = User::where('email', '=', request('email'))->first('id'); 
        // if($userId){
            $id = $userId->id;  
            $accessTokenDetails = DB::table('oauth_access_tokens')->where('user_id', '=', $id)->first(['id', 'expires_at']);
            $refreshTokenDetails = DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $accessTokenDetails->id)->first(['id', 'expires_at']);
           
            $currentTime = Carbon::now();
            $expiryDate = new Carbon($accessTokenDetails->expires_at);
           if($currentTime > $expiryDate){          /// dates not working properly 
               return $this->generateTokens();
           }else{
               return response()->json(['success' => "Login Success"], $this-> successStatus); 
           }   
        // }
        // $client = DB::table('oauth_clients')
        //     ->where('password_client', true)
        //     ->first();

        // $data = [
        //     'grant_type' => 'password',
        //     'client_id' => $client->id,
        //     'client_secret' => $client->secret,
        //     'username' => request('email'),
        //     'password' => request('password'),
        // ];
        // $request = Request::create('/oauth/token', 'POST', $data);
        // return app()->handle($request);
}  else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    //   return Route::dispatch($request);
}

public function generateTokens()
{    
    $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();

        $data = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => request('email'),
            'password' => request('password'),
        ];
        $request = Request::create('/oauth/token', 'POST', $data);
        return app()->handle($request); 
}

    public function logout()
    {
        $accessToken = auth()->user()->token();

        $refreshToken = DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();

        return response()->json(['status' => 200]);
    }

}
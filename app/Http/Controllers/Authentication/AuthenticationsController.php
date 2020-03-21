<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationsController extends Controller
{

    /**
     * AuthenticationsController constructor.
     */
    public function __construct()
    {

    }

    public function authenticate(){
        try{
            $credentials = request()->only('email','password');
            $rules = ['email' => 'required|max:255', 'password' => 'required|min:4'];
            $validator = Validator::make($credentials, $rules);

            if($validator->fails()){
                $error = $validator->messages();
                return response()->json(['status'=>false, 'result'=>null, 'message'=>null, 'error'=> $error],500);
            }
            if(!Auth::attempt($credentials)) {
                return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! invalid credential has been used!','error'=> 'invalid credential'], 401);
            }
            $user = Auth::user();
            if($user instanceof User) {
                if($user->is_active) {
                    $token = $user->createToken('negarit_gateway_v3')->accessToken;
                    return response()->json(['status'=>true, 'message'=>'Authentication Successful', 'result'=>$user, 'token'=>$token],200);
                } else {
                    return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! inactive account', 'error'=>'inactive account'],500);
                }
            } else {
                return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! authentication failed', 'error'=>'authentication failed'],500);
            }
        }catch (\Exception $exception){
            return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! exception has occurred', 'error'=>$exception->getMessage()],500);
        }
    }
    public function register()
    {
        try {
            $credentials = request()->only( 'full_name', 'phone', 'email', 'password');
            $rules = [
                'full_name' => 'required|min:4|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required'
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
                $error = $validator->messages();
                return response()->json(['status' => false, 'message' => "Whoops! Invalid Input", 'error' => $error], 500);
            }
            $old_user = User::where('email', '=', $credentials['email'])->first();
            if ($old_user instanceof User) {
                return response()->json(['status' => false, 'message' => 'This phone or email is already taken!', 'error' => 'Invalid Email and Phone Number'], 500);
            } else {
                $new_user = new User();
                $new_user->role_id = 4;
                $new_user->full_name = $credentials['full_name'];
                $new_user->email = $credentials['email'];
                $new_user->password = bcrypt($credentials['password']);
                if ($new_user->save()) {
                    return response()->json(['status' => true, 'message' => 'registered successfully', 'result' => $new_user], 200);
                } else {
                    return response()->json(['status' => false, 'message' => 'whoops! unable to create a user! please try again', 'result' => null, 'error'=>null], 500);
                }
            }
        } catch (\Exception $exception) {
            return response()->json(['status'=>false, 'result'=>null, 'message'=>'whoops! exception has occurred', 'error'=>$exception->getMessage()],500);
        }
    }
}

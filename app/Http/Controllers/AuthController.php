<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\SendCodeVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ResponseTrait;
    public function register (RegisterRequest $request){
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->apiResponse($user,'User Created successfully , waiting for verification !',200);
    }

    public function login (LoginRequest $request){
        $request->validated();

        if (!$token=auth('api')->attempt($request->only(['email', 'password'])))
            return $this->apiResponse(null,'Unauthorized !',403);
        $user = auth('api')->user();
        $user['token'] = $token;
        return $this->apiResponse($user,'User authenticated successfully !',200);
    }

    public function logout(){
        if(!auth('api')->user())
            return $this->apiResponse(null,'User already unauthenticated !',400);
        auth('api')->logout();
        return $this->apiResponse(null,'User successfully logged out !',200);
    }

    public function requestVerify (Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);
        $code = mt_rand(100000,999999);
        $hashedCode = Hash::make($code);;
        $user = User::query()->where('email',$request->email)->update(['code'=>$hashedCode]);
        Mail::to($request->email)->send(new SendCodeVerification($code));

        return $this->apiResponse(null,'email sent successfully !',200);
    }

    public function verify (Request $request)
    {
        $request->validate([
            'code'=>'required|string',
            'user_id' => 'required'
        ]);
        $user = User::find($request->user_id)->first();
        $hashedCode = $user->code;
        if (Hash::check($request->code, $hashedCode)) {
            $user->email_verified_at = Carbon::now();
            $user->save();
            return $this->apiResponse($user, 'User successfully verified !', 200);
        }
        return $this->apiResponse(null,'Verification process failed !',400);
    }

}

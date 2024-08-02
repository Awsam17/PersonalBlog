<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $token = auth('api')->attempt($request->only(['email', 'password']));
        $data = $user;
        $data['token'] = $token;
        return $this->apiResponse($data,'User registered successfully !',200);
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

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->all();
       
        if (!auth()->attempt($data)) {
            return response()->json('somthing went wrog', 422);
        }
        $user = auth()->user();
        $tokenResult = $user->createToken('userToken');
        $tokenModel = $tokenResult->token;
        if ($request->remember_me)
            $tokenModel->expires_at = Carbon::now()->addWeeks(1);
        $tokenModel->save();
        return response()->json([
            'user' => $user,
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer'
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        
        $data['password'] = bcrypt($request->password);
        $user = User::create($data);
        $accessToken = $user->createToken('UserToken')->accessToken;
        return response()->json([
            'user' => $user,
            'token' => $accessToken,
            'token_type' => 'Bearer'
        ]);
    }
}

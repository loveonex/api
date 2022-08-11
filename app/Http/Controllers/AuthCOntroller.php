<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthCOntroller extends Controller
{
    public function register(Request $request)
    {
        $rule = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ];
        $validator = Validator::make($request->all(),  $rule);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'error' => $validator->errors()->toArray(),
                ]
            );
        }
        $user = new User(
            [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]
        );
        $user->save();
        return response()->json(
            [
                'status' => true
            ]
        );
    }

    public function login(Request $request){
        $rule = [
            'email' => "required|string|email",
            'password' => "required|string",
        ];
        $validator = Validator::make($request->all(), $rule);
        if($validator->fails()) {
            return response()->json([
                'status' => "Fails",
                "error" => $validator->errors()->toArray()
            ]);
        }
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => "Sai tài khoản hoặc mật khẩu"
            ]);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeek(1);
        $token->save();
        return response()->json([
            'status' => 'success',
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)
                ->toDateTimeString()
        ]);
    }
    public function user(){
        return response()->json([
            "Họ tên" => "Nguyễn Văn A"
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'status' => "success"
        ]);
    }
}

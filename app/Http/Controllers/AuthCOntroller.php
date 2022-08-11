<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
    }
}

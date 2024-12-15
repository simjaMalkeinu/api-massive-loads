<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
            "confirm_password" => "required|same:password"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Validation error",
                "data" => $validator->errors()->all()
            ]);
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        $token = $user->createToken("TokenApp")->accessToken;
        $data = [];

        $data['token'] = $token;
        $data['user'] = $user;



        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(["email" => $request->email, "password" => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken("TokenApp")->accessToken;
            $data = [];

            $data['token'] = $token;
            $data['username'] = $user->name;
            $data['email'] = $user->email;

            return response()->json([
                "status" => 200,
                "response" => $data
            ]);
        }

        return response()->json([
            "status" => 0,
            "message" => "User unauthentication",
            "response" => null
        ]);
    }
}

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
        $messages = [
            "name.required" => "El nombre es obligatorio.",
            "email.required" => "El correo electrónico es obligatorio.",
            "email.email" => "El correo electrónico debe ser una dirección válida.",
            "email.unique" => "El correo electrónico ya está registrado.",
            "password.required" => "La contraseña es obligatoria.",
            "password.min" => "La contraseña debe tener al menos 8 caracteres.",
            "password.regex" => "La contraseña debe incluir al menos una letra mayúscula, una minúscula, un número y un carácter especial.",
            "confirm_password.required" => "Debe confirmar la contraseña.",
            "confirm_password.same" => "Las contraseñas no coinciden."
        ];

        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255", // Asegura que sea texto y no exceda 255 caracteres
            "email" => "required|email|max:255|unique:users,email", // Correo único en la tabla users
            "password" => [
                "required",
                "string",
                "min:8", // Longitud mínima de 8 caracteres
                "regex:/[a-z]/", // Debe contener al menos una letra minúscula
                "regex:/[A-Z]/", // Debe contener al menos una letra mayúscula
                "regex:/[0-9]/", // Debe contener al menos un número
                "regex:/[@$!%*?&]/", // Debe contener al menos un carácter especial
            ],
            "confirm_password" => "required|same:password"
        ], $messages);

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

        // $token = $user->createToken("TokenApp")->accessToken;
        $data = [];

        // $data['token'] = $token;
        $data['username'] = $user->name;
        $data['email'] = $user->email;



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

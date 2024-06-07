<?php

namespace App\Http\Controllers;

use App\Models\Tokens;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use App\Models\User;


class UserController extends Controller
{
    public static function index () : JsonResponse
    {
        $data = User::all();

        return response()->json($data);
    }
    public static function login(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Lūdzu ievadiet lietotājvārdu!',
            'password.required' => 'Lūdzu ievadiet paroli!'
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Neizdevās pieslēgties!',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if username and password match with the database
        $user = User::where('username', $request->input('username'))->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['Nepareiz lietotājvārds vai parole!'],
                ]
            ], 422);
        }

        $token = Tokens::where('id', $user->id)->first();

        return response()->json([
            'token' => $token['token'],
            'status' => 200
        ], 200);

    }

    function userVerify(Request $request) {

        return response()->json(['status' => '200', 'user' => $request->authenticatedUser]);

    }

    function register () {
        $data = [
            'username' => 'Admin',
            'password' => '$2y$10$Y4nEXtcAMFQXeDZnIgAs2.sRI.Pu0Z0fVOiTSyc.oXTr8A3QChe6S',
            'token_id' => 1
        ];
        $token = [
            'token' => 'j7hYinIDjvT0EcnAnSelibk5n1WLvyQhxIWhgXffb3sVUxDyVbTSuUDVsPB4'
        ];

        Tokens::create($token);
        User::create($data);
    }
}

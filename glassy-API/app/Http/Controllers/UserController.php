<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;


class UserController extends Controller
{
    function login(Request $request) {

        //Init data
        $data = [
            'user' => $user = $request->input('username'),
            'pass' => $pass = $request->input('password'),
            'userErr' => '',
            'passErr' => '',
        ];

        //Check if username and password match with db
        $user = User::where('username', $request->input('username'))->first();

        if(!$user || !Hash::check($request->password, $user->password)) {

            $data['passErr'] = 'Lietotājvārds vai parole ir nepareiza!';

        }

        //Check if user field is empty
        if(empty($data['user'])) {
            $data['userErr'] = 'Lūdzu ievadiet lietotājvārdu!';
        }

        //Check if pass field is empty
        if(empty($data['pass'])) {
            $data['passErr'] = 'Lūdzu ievadiet paroli!';
        }

        //If error check is passed return token json
        if(empty($data['userErr']) && empty($data['passErr'])) {

            return response()->json([
                'token' => $user['token'],
                'success' => true
            ]);

        }

        //If error pass failed return error json
        return response()->json([
            'userErr' => $data['userErr'],
            'passErr' => $data['passErr'],
            'success' => false
        ]);

    }

    function register () {
        $data = [
            'username' => 'Marija',
            'password' => '$2y$10$Y4nEXtcAMFQXeDZnIgAs2.sRI.Pu0Z0fVOiTSyc.oXTr8A3QChe6S',
            'token' => 'j7hYinIDjvT0EcnAnSelibk5n1WLvyQhxIWhgXffb3sVUxDyVbTSuUDVsPB4'
        ];

        User::create($data);
    }
}

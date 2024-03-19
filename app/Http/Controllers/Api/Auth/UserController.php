<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $name = $request->name;
            $user =  User::create([
                'name' => $name,
                'email' => $request->email,
                'password' => bcrypt($request->password),

            ]);
            $token = $user->createToken('UserAuth')->accessToken;

            return response()->json(['token' => $token, 'user' => $user], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => ApiService::error_processor($validator)], 403);
        }
        try {
            $user = User::where(['email' => $request['email']])->first();
            if (isset($user)) {
                $data = [
                    'email' => $user->email,
                    'password' => $request->password
                ];

                if (Auth::attempt($data)) {
                    $token = $user->createToken('UserAuth')->accessToken;
                    return response()->json(['token' => $token, 'user' => $user], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }


        $errors = [];
        array_push($errors, ['code' => 'auth-001', 'message' => 'Invalid credential.']);
        return response()->json([
            'errors' => $errors
        ], 401);
    }
}

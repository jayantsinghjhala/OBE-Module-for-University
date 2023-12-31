<?php

namespace App\Http\Controllers\Auth_API;

use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
// use Tymon\JWTAuth\Facades\JWTFactory;


class LoginController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                $response_code = 400;
                $response = [
                    'status' => '0',
                    'message' => $validator->errors()->toArray(),
                ];
            } else {
                $credentials = $request->only('email', 'password');

                if ($token = JWTAuth::attempt($credentials)) {
                    $response_code = 200;
                    $response = [
                        'status' => '1',
                        'message' => 'Login Successful',
                        'token' => $token,
                        'user' => JWTAuth::user(),
                    ];
                } else {
                    $response_code = 401;
                    $response = [
                        'status' => '0',
                        'message' => 'Invalid credentials',
                    ];
                }
            }
        } catch (\Exception $e) {
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }

        return response()->json($response, $response_code);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate(); // Invalidate the user's token
            JWTAuth::unsetToken();
            $response_code = 200;
            $response = [
                'status' => '1',
                'message' => 'Logged Out Successfully',
                'user'=>JWTAuth::user()
            ];
        } catch (\Exception $e) {
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }

        return response()->json($response, $response_code);
    }

}
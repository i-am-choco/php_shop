<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * 注册功能
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public  function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::factory()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
            ]);

            return response()->json(["user" => $user, 'message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {

        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('token')->plainTextToken;

                return response()->json([
                    'user' => $user,
                    'access_token' => $token,
                    'message' => 'Login successful'
                ]);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }catch (\Exception $e)
        {
            return  response()->json(['message' => $e->getMessage()], 500);
        }

    }
}

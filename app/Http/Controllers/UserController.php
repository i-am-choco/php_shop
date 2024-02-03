<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

        return response()->json(["user" => $user, 'message' => 'Success' ]);
    }
}

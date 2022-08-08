<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthenticatedController extends Controller
{
    /**
     * Login
     */
    public function login(LoginRequest $request)
    {

        $data = $request->only(User::UPDATE_ONLY);

        if (!Auth::attempt($data)) {
            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }
        $access_token = $request->user()->createToken($request->device)->plainTextToken;
        return response()->json(['access_token' => $access_token], Response::HTTP_OK);
    }

    /**
     * logout
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([], Response::HTTP_OK);
    }
}

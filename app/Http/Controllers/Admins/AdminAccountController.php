<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAccountController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->except("login");
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false,'errors' => $validate->errors()], 403);
        }

        $ttl = 60 * 24;
        if($request->has('remember')) {
            $ttl *= 30;
        }
        $user = User::where('email', $request->email)->first();
        if($user->user_role !== 'admin') {
            return response()->json(['success' => false,'message' => 'unauthorized'], 401);
        }

        $token = Auth::setTTL($ttl)->attempt($request->only('email','password'));
        if(!$token) {
            return $this->failed("Wrong email or password");
        }
        $user = Auth::user();
        return $this->success([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return $this->success("successfully logged out");
    }
}

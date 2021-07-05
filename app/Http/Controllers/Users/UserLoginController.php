<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserLoginController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users'])->except('login');
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

        $token = Auth::setTTL($ttl)->attempt($request->only('email','password'));
        if(!$token) {
            return $this->failed("Wrong email or password");
        }
        $user = Auth::user();
        /*
        TODO : remove this when implemented in frontEnd
        if (! $user->isEmailVerified()) {
            return $this->failed("Email not verified, please check your email to verify");
        }
        */
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

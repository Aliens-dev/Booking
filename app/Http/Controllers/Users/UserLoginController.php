<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserLoginController extends ApiController
{

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            $this->errors($validate->errors()->toArray());
        }
        $ttl = 60 * 24;
        if($request->has('remember')) {
            $ttl *= 30;
        }

        $token = Auth::setTTL($ttl)->attempt($request->only('email','password'));
        if(!$token) {
            $this->failed("Wrong email or password");
        }
        $this->success($token);
    }

    public function logout(Request $request)
    {

    }
}

<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserAccountController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'fname' => 'required|min:3|max:30',
            'lname' => 'required|min:3|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'phone_number' => 'required|regex:/^0[567]{1}[0-9]{8}$/i',
            'dob' => 'required|date',
            'user_role' => 'required|in:client,renter',
        ];
        $validated = Validator::make($request->all(), $rules);
        if($validated->fails()) {
            return response()->json(['success' => false, 'errors' =>$validated->errors()], 403);
        }

        $user = new User();
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->phone_number = $request->phone_number;
        $user->dob = $request->dob;
        $user->user_role = $request->user_role;
        $user->save();

        return response()->json(['success'=> true], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'fname' => 'required|min:3|max:30',
            'lname' => 'required|min:3|max:30',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required|confirmed',
            'phone_number' => 'required|regex:/^0[567]{1}[0-9]{8}$/i',
            'address' => 'required',
            'dob' => 'required|date',
        ];

        $validated = Validator::make($request->all(), $rules);
        if($validated->fails()) {
            return response()->json(['success' => false, 'errors' =>$validated->errors()], 403);
        }
        $inspect = Gate::inspect('update', $user);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'errors' =>$inspect->message()], 403);
        }

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->phone_number = $request->phone_number;
        $user->dob = $request->dob;
        $user->save();

        return response()->json(['success'=> true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        $inspect = Gate::inspect('delete', $user);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'errors' =>$inspect->message()], 403);
        }
        try {
            $isDeleted = $user->delete();
        }catch (\Exception $e) {
            return \response()->json(['success' => false, 'errors' => $e->getMessage()], 403);
        }
        return response()->json(['success' => true], 200);
    }
}

<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Renter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserAccountController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:users','verified:verification.verify'])->except('store');
    }


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
            'user_role' => 'required|in:client,renter',
            'dob' => 'required|date',
        ];
        $validated = Validator::make($request->all(), $rules);
        if($validated->fails()) {
            return response()->json(['success' => false, 'errors' =>$validated->errors()], 403);
        }

        Renter::create($validated->validated())->sendEmailVerificationNotification();
        return response()->json(['success'=> true], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Renter $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Renter $user)
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

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' =>$validate->errors()], 403);
        }
        $inspect = Gate::inspect('update', $user);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'errors' =>$inspect->message()], 403);
        }

        $user->update($validate->validated());

        return response()->json(['success'=> true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Renter $user
     * @return JsonResponse
     */
    public function destroy(Renter $user)
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

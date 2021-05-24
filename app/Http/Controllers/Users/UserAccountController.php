<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Models\Renter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class UserAccountController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users','verified:verification.verify'])->except('store','index','show');
    }

    public function index(Request $request)
    {
        $users = User::paginate(10);
        return $this->success($users);
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
            'phone_number' => 'required|sometimes|regex:/^0[567]{1}[0-9]{8}$/i',
            'user_role' => 'required|in:client,renter',
            'dob' => 'required|date',
            'profile_pic' => 'required|sometimes|image|mimes:jpg,png',
            'identity_pic' => 'required|sometimes|image|mimes:jpg,png',
            'affiliate' => 'required|sometimes'
        ];
        $validated = Validator::make($request->all(), $rules);
        if($validated->fails()) {
            return response()->json(['success' => false, 'errors' =>$validated->errors()], 403);
        }
        $data = $validated->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $user->email_verified_at = Carbon::now();
        $user->save();
        $user->sendEmailVerificationNotification();

        if($request->has('profile_pic')) {
            $user->profile_pic = "uploads/{$user->user_role}/" . $request->file('profile_pic')
                    ->storePubliclyAs(
                        $user->id,
                        $request->file('profile_pic')->getClientOriginalName(),
                        $user->user_role
                    );
        }

        if($request->has('identity_pic')) {
            $user->identity_pic =  "uploads/{$user->user_role}/" . $request->file('profile_pic')
                    ->storePubliclyAs(
                        $user->id,
                        $request->file('identity_pic')->getClientOriginalName(),
                        $user->user_role
                    );
        }

        $user->save();
        return response()->json(['success'=> true, 'message' => $user], 201);
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);
        if(is_null($user)) {
            return $this->failed();
        }
        return $this->success($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'fname' => 'required|min:3|max:30',
            'lname' => 'required|min:3|max:30',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required|confirmed',
            'phone_number' => 'required|regex:/^0[567]{1}[0-9]{8}$/i',
            'dob' => 'required|date',
            'profile_pic' => 'sometimes|required|image|mimes:jpg,png',
            'identity_pic' => 'sometimes|required|image|mimes:jpg,png',
        ];

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' =>$validate->errors()], 403);
        }
        $inspect = Gate::inspect('update', $user);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'errors' =>$inspect->message()], 401);
        }

        $user->update($validate->validated());

        $user->profile_pic = str_replace("uploads/{$user->user_role}/","", $user->profile_pic);
        $user->identity_pic = str_replace("uploads/{$user->user_role}/","",$user->identity_pic);
        Storage::disk($user->user_role)->delete($user->profile_pic);
        Storage::disk($user->user_role)->delete($user->identity_pic);
        $user->profile_pic = "uploads/{$user->user_role}/" . $request->file('profile_pic')
                ->storePubliclyAs(
                    $user->id,
                    $request->file('profile_pic')->getClientOriginalName(),
                    $user->user_role
                );
        $user->identity_pic =  "uploads/{$user->user_role}/" . $request->file('profile_pic')
                ->storePubliclyAs(
                    $user->id,
                    $request->file('identity_pic')->getClientOriginalName(),
                    $user->user_role
                );

        $user->save();
        return response()->json(['success'=> true, 'message' => $user], 200);
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
        return response()->json(['success' => $isDeleted], 200);
    }
}

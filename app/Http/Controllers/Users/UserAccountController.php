<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Models\Renter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class UserAccountController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->only('approve');
        $this->middleware(['auth:users','verified:verification.verify'])->except('approve','store','index','show');
    }

    public function index(Request $request)
    {
        $users = User::basic()->verified()->paginate(10);
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
            'user_role' => 'required|in:client,renter,admin',
            'dob' => 'required|date',
            'profile_pic' => 'required|sometimes|image|mimes:jpg,png',
            'identity_pic' => 'required|sometimes|image|mimes:jpg,png',
            'affiliate' => 'required|sometimes'
        ];
        $validated = Validator::make($request->all(), $rules);
        if($validated->fails()) {
            return response()->json(['success' => false, 'errors' =>$validated->errors()], 403);
        }
        if($request->user_role === 'admin' && !auth()->user()->user_role == 'admin') {
            return response()->json(['success' => false, 'message' =>'unauthorized'], 401);
        }
        $data = $validated->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $user->email_verified_at = Carbon::now();
        $user->save();
        //$user->sendEmailVerificationNotification();

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
        $user = User::basic()->find($id);
        if(is_null($user)) {
            return response()->json(['success'=> true, 'message' => 'no record found'], 403);
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
    public function update(Request $request, $userId)
    {
        /* check if user exists */
        $user = User::find($userId);
        if(is_null($user)) {
            return response()->json(['success'=> true, 'message' => 'no record found'], 403);
        }

        $rules = [
            'fname' => 'required|min:3|max:30',
            'lname' => 'required|min:3|max:30',
            'email' => [
                'required',
                'email', 
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'required',
            'phone_number' => 'sometimes|required|regex:/^0[567]{1}[0-9]{8}$/i',
            'dob' => 'required|date',
            'profile_pic' => 'sometimes|required|image|mimes:jpg,png',
            'identity_pic' => 'sometimes|required|image|mimes:jpg,png',
        ];
        /* validate */
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' =>$validate->errors()], 403);
        }
        /* check if the password match */
        if(!Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' =>"password does not match"], 401);
        }
        /* check if the user is authorized to update */
        $inspect = Gate::inspect('update', $user);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'errors' =>$inspect->message()], 401);
        }
        /* hash the password */
        $validate = $validate->validated();
        $validate['password'] = bcrypt($request->password);
        $user->update($validate);

        /* if there is a profile_pic in the request update it */
        if($request->hasFile('profile_pic')) {
            $user->profile_pic = str_replace("uploads/{$user->user_role}/","", $user->profile_pic);
            Storage::disk($user->user_role)->delete($user->profile_pic);
            $user->profile_pic = "uploads/{$user->user_role}/" . $request->file('profile_pic')
                    ->storePubliclyAs(
                        $user->id,
                        $request->file('profile_pic')->getClientOriginalName(),
                        $user->user_role
                    );
        }
        /* if there is a identity in the request update it */
        if($request->hasFile('identity_pic')) {
            $user->identity_pic = str_replace("uploads/{$user->user_role}/","",$user->identity_pic);
            Storage::disk($user->user_role)->delete($user->identity_pic);
            $user->identity_pic =  "uploads/{$user->user_role}/" . $request->file('profile_pic')
                    ->storePubliclyAs(
                        $user->id,
                        $request->file('identity_pic')->getClientOriginalName(),
                        $user->user_role
                    );
        }

        /* save all */
        $user->save();
        return response()->json(['success'=> true, 'message' => $user], 200);
    }
    public function password_reset(Request $request,$userId) {
        $user = User::find($userId);
        if(is_null($user)) {
            return response()->json(['success'=> true, 'message' => 'no record found'], 403);
        }
        $rules = [
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ];

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' =>$validate->errors()], 403);
        }

        if(!Hash::check($request->old_password, $user->password)) {
            return response()->json(['success' => false, 'message' =>"password does not match"], 401);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        
        return response()->json(['success'=> true, 'message' => $user], 200);
    }

    public function approve($userId) {
        $user = User::find($userId);
        if(is_null($user)) {
            return response()->json(['success'=> true, 'message' => 'no record found'], 403);
        }
        $user->verified = 1;
        $user->save();
        return response()->json(['success' => true], 200);
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy($userId)
    {
        $user = User::find($userId);
        if(is_null($user)) {
            return response()->json(['success' => false, 'message' => "no record found"], 403); 
        }
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

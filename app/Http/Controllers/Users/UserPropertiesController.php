<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use App\Models\Renter;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class UserPropertiesController extends ApiController
{

    public function index(Request $request, $userId)
    {
        $user = Renter::find($userId);
        if(is_null($user)) {
            return $this->failed("User doesnt have any records!");
        }
        $properties = $user->properties()->get();
        return $this->success($properties);
    }
}

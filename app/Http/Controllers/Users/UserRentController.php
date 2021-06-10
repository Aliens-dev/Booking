<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\User;

class UserRentController extends ApiController
{

    public function index($id)
    {
        $user = Client::find($id);
        if(is_null($user)) {
            return response()->json(['success' => false, 'message' => 'no record found'], 403);
        }
        $properties = $user->properties()->withAll()->paginate(10);

        foreach ($properties as $property) {
            $property->reservation = [
                'id' => (int)$property->pivot->id,
                #'receipt' => url('/') . '/' . $property->pivot->receipt,
                'start_time' => $property->pivot->start_time,
                'end_time' => $property->pivot->end_time,
            ];
        }
        return response()->json(['success' => true, 'message' => $properties],200);
    }
}

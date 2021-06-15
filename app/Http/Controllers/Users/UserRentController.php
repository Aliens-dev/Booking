<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\User;

class UserRentController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users', 'client.auth']);
    }

    public function index($id)
    {
        /*
        if(auth()->id() != $id) {
            return response()->json(['success' => false,'message' => 'unauthorized access'], 401);
        }
        */
        $user = Client::find($id);
        if(is_null($user)) {
            return response()->json(['success' => false, 'message' => 'no record found'], 403);
        }

        $properties = $user->properties()->withAll()->paginate(10);

        foreach ($properties as $property) {
            $property->reservation = [
                'id' => (int)$property->pivot->id,
                'renter_id' => (int)$property->pivot->renter_id,
                'client_id' => (int)$property->pivot->client_id,
                'property_id' => (int)$property->pivot->property_id,
                'receipt_url' => $property->pivot->receipt_url,
                'receipt_status' => $property->pivot->receipt_status,
                'start_time' => $property->pivot->start_time,
                'end_time' => $property->pivot->end_time,
            ];
        }
        return response()->json(['success' => true, 'message' => $properties],200);
    }
}

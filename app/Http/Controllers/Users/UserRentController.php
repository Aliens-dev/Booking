<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\Property;
use App\Models\Renter;
use App\Models\Reservation;
use App\Models\User;

class UserRentController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users']);
    }

    public function index($id)
    {
        $user = User::find($id);
        if(is_null($user)) {
            return response()->json(['success' => false, 'message' => 'no record found'], 403);
        }
        $reservations = [];
        if($user->user_role == 'client') {
            $reservations = Reservation::where('client_id', $id)->paginate(10);
        }else if($user->user_role == 'renter') {
            $reservations = Reservation::where('renter_id', $id)->where('receipt_status', 'approved')->paginate(10);
        }
        foreach ($reservations as $reservation) {
            $reservation->property = Property::withAll()->find($reservation->property_id);
        }
        return response()->json(['success' => true, 'message' => $reservations],200);

    }
}

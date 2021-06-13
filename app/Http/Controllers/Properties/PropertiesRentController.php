<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PropertiesRentController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:users'])->only('destroy');
        $this->middleware(['auth:users','client.auth'])->only('store','verify','update');
        #$this->middleware(['auth:users','renter.auth'])->only('update');
    }


    /**
     * @param $propertyId
     * @return JsonResponse
     */
    public function index($propertyId)
    {
        $rents = Reservation::where('property_id', $propertyId)->get();
        return response()->json(['success' => true, 'message' => $rents], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $propertyId
     * @return JsonResponse
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);
        if (is_null($property)) {
            return response()->json(['success' => false,'message' => 'record not found'], 403);
        }
        if($property->status != 'available') {
            return response()->json(['success' => false, 'message' => 'property is not available'], 403);
        }
        $rules = [
            'start_time' => 'required|date|after_or_equal:'. date('Y-m-d'),
            'end_time' => 'required|date|after:start_time',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success'=> false, 'errors' => $validate->errors()], 403);
        }
        $start_time = date('Y-m-d',strtotime($request->start_time));
        $end_time = date('Y-m-d',strtotime($request->end_time));

        $client = Client::find(auth()->id());

        $client->properties()->attach($property->id,[
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);
        $property->status = 'pending';
        $property->save();
        return response()->json(['success'=> true], 201);
    }

    /**
     * @param $propertyId
     * @param $rentId
     * @return JsonResponse
     */
    public function show($propertyId, $rentId)
    {
        $reservation = Reservation::where('id',$rentId)->where('property_id', $propertyId)->first();
        return response()->json(['success' => true, 'message' => $reservation], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $propertyId
     * @param $rentId
     * @return JsonResponse
     */

    public function verify(Request $request, $propertyId, $rentId)
    {
        $reservation = Reservation::
            where('id', $rentId)
            ->where('client_id', auth()->id())
            ->where('property_id',$propertyId)
            ->first();
        if(is_null($reservation)) {
            return response()->json(['success' => false, 'message' => 'no record found'], 403);
        }
        $inspect = Gate::inspect('verify', $reservation);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Access'], 401);
        }
        $rules = [
            'receipt' => 'required|image|mimes:jpg,png'
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false, 'message' => $validate->errors()], 403);
        }
        $receipt = '';
        if($request->hasFile('receipt')) {
            $property = Property::find($reservation->property_id);
            $receipt =  "uploads/property/" .
                    $request->file('receipt')
                    ->storeAs($property->id . '/reservation', $request->file('receipt')->getClientOriginalName());
        }
        $reservation->receipt_status = 'waiting_approval';
        $reservation->receipt = $receipt;
        $reservation->save();
        return response()->json(['success' => true, 'message' => $reservation], 200);
    }

    public function approve(Request $request, $propertyId, $rentId)
    {
        $reservation = Reservation::
            where('id', $rentId)
            ->where('property_id',$propertyId)
            ->first();
        if(is_null($reservation)) {
            return response()->json(['success' => false, 'message' => 'no record found'], 403);
        }
        $inspect = Gate::inspect('approve', $reservation);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Access'], 401);
        }

        $reservation->receipt_status = 'approved';
        $reservation->save();

        $property = Property::find($propertyId);
        $property->status = 'approved';
        $property->save();

        return response()->json(['success' => true, 'message' => $reservation], 200);
    }

    public function decline(Request $request, $propertyId, $rentId)
    {
        $reservation = Reservation::
        where('id', $rentId)
            ->where('property_id',$propertyId)
            ->first();
        if(is_null($reservation)) {
            return response()->json(['success' => false, 'message' => 'no record found'], 403);
        }
        $inspect = Gate::inspect('decline', $reservation);
        if($inspect->denied()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Access'], 401);
        }
        $reservation->receipt_status = 'declined';
        $reservation->save();

        $property = Property::find($propertyId);
        $property->status = 'available';
        $property->save();

        return response()->json(['success' => true, 'message' => $reservation], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Property $property
     * @return JsonResponse
     */
    public function destroy(Property $property)
    {
        $inspect = Gate::inspect('cancelRent', $property);
        if($inspect->denied()) {
            return response()->json(['success' => false ], 401);
        }
        if(auth()->user() instanceof Client) {
            auth()->user()->properties()->detach($property->id);
        }else {
            Reservation::where('property_id', $property->id)->delete();
        }
        $property->status = 'available';
        $property->save();

        return response()->json(['success'=> true], 200);
    }
}

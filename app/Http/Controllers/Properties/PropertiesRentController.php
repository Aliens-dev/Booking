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
        $this->middleware(['auth:users','client.auth'])->only('store');
        $this->middleware(['auth:users','renter.auth'])->only('update');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Property $property
     * @return JsonResponse
     */
    public function store(Request $request, Property $property)
    {
        $rules=  [
            'start_time' => 'required|date|after_or_equal:'. date('Y-m-d'),
            'end_time' => 'required|date|after:start_time',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success'=> false], 403);
        }
        $start_time = Carbon::createFromFormat('Y-m-d',$request->start_time)->toDateString();
        $end_time = Carbon::createFromFormat('Y-m-d',$request->end_time)->toDateString();

        auth()->user()->properties()->attach($property->id,[
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);
        $property->status = 'pending';
        $property->save();
        return response()->json(['success'=> true], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Property $property
     * @return JsonResponse
     */
    public function update(Request $request, Property $property)
    {
        $inspect = Gate::inspect('update', $property);
        if($inspect->denied()) {
            return response()->json(['success' => false ], 401);
        }
        $property->status = 'approved';
        $property->save();
        return response()->json(['success'=> true], 200);
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
<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
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
        $this->middleware(['auth:users', 'client.auth']);
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

        return response()->json(['success'=> true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
        auth()->user()->properties()->detach($property->id);
        return response()->json(['success'=> true], 200);
    }
}

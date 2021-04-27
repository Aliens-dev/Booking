<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kossa\AlgerianCities\Wilaya;

class PropertiesController extends Controller
{
    use RefreshDatabase;

    public function store(Request $request)
    {

        $rules = [
            'title' => 'required|min:3|max:100',
            'state' => ['required', Rule::in(wilayas())],
            'city' => ['required', Rule::in(communes())],
            'street' => 'required|min:3|max:255',
            'price' => 'required',
        ];

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }

        $property = new Property();

        $property->title = $request->title;
        $property->state = $request->state;
        $property->city = $request->city;
        $property->street = $request->street;
        $property->description = $request->description;
        $property->price = $request->price;
        $property->type = $request->type;
        $property->save();
        return response()->json(['success'=> true],201);
    }
}

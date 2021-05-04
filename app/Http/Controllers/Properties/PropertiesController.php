<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;

class PropertiesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:users','renter.auth']);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|min:3|max:100',
            'state' => ['required', Rule::in(wilayas())],
            'city' => ['required', Rule::in(communes())],
            'street' => 'required|min:3|max:255',
            'price' => 'required|integer|min:200',
            'type' => 'required',
            'rooms' => 'required|min:1|integer',
            'bedrooms' => 'required|min:1|integer',
            'bathrooms' => 'required|min:1|integer',
            'beds' => 'required|min:1|integer',
            'images' => 'required|max:10',
            'images.*' => 'image|mimes:jpg,bmp,png',
            'description' => 'sometimes|required|max:500',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }

        $wilaya = Wilaya::where('name', $request->state)->first();
        $commune = Commune::where('wilaya_id', $wilaya->id)->where('name', $request->city)->first();

        if(! $commune) {
            return response()->json(['success' => false, 'errors' => "Commune Name doesn't correspond to any Wilaya"],403);
        }
        $property = auth()->user()->properties()->create($validate->validated());

        if($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }
        return response()->json(['success'=> true],201);
    }

    public function update(Request $request, Property $property) {
        $rules = [
            'title' => 'required|min:3|max:100',
            'state' => ['required', Rule::in(wilayas())],
            'city' => ['required', Rule::in(communes())],
            'street' => 'required|min:3|max:255',
            'price' => 'required|integer|min:200',
            'type' => 'required',
            'rooms' => 'required|min:1|integer',
            'bedrooms' => 'required|min:1|integer',
            'bathrooms' => 'required|min:1|integer',
            'beds' => 'required|min:1|integer',
            'images' => 'sometimes|required|max:10',
            'images.*' => 'sometimes|image|mimes:jpg,bmp,png',
            'description' => 'sometimes|required|max:500',
        ];

        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }

        if($request->type !== $property->type) {
            return response()->json(['success' => false ], 403);
        }

        auth()->user()->properties()->update(collect($validate->validated())->except('images')->toArray());

        if($request->hasFile('images')) {
            $property->images()->delete();
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }
        return response()->json(['success' => true], 200);
    }

    public function destroy(Request $request, Property $property)
    {
        $inspect = Gate::inspect('delete', $property);
        if($inspect->denied()) {
            return response()->json(['success' => false ], 401);
        }
        $property->delete();
        return response()->json(['success' => true], 200);
    }
}

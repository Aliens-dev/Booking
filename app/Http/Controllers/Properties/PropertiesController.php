<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;
use function PHPUnit\Framework\assertEquals;

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
            'price' => 'required|integer|min:200',
            'type' => 'required',
            'rooms' => 'required|min:1|integer',
            'images' => 'required|max:10',
            'images.*' => 'image|mimes:jpg,bmp,png',
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
        $property = auth()->user()->properties()->create($request->all());

        if($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }

        return response()->json(['success'=> true],201);
    }

    public function update(Request $request, $id) {
        $property = Property::find($id);
        $rules = [
            'title' => 'required|min:3|max:100',
            'state' => ['required', Rule::in(wilayas())],
            'city' => ['required', Rule::in(communes())],
            'street' => 'required|min:3|max:255',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }

        $property = auth()->user()->properties()->update($request->all());

        return response()->json(['success' => true], 200);
    }
}

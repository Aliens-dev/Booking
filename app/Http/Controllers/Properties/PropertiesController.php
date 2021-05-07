<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as VALRule;
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

        $validate = Validator::make($request->all(), $this->rules());
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        $wilaya = Wilaya::where('name', $request->state)->first();
        $commune = Commune::where('wilaya_id', $wilaya->id)->where('name', $request->city)->first();

        if(! $commune) {
            return response()->json(['success' => false, 'errors' => "Commune Name doesn't correspond to any Wilaya"],403);
        }

        $property_type = PropertyType::where('type', $request->type)->first();
        $data = collect($validate->validated())->except('images','rules')->put('type_id',$property_type->id)->toArray();
        $property = auth()->user()->properties()->create($data);

        $this->updatePivot($request,$property,Rule::class, 'rules');
        $this->updatePivot($request,$property, Facility::class,'facilities');
        $this->updatePivot($request,$property,Amenity::class,'amenities');

        if($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }
        return response()->json(['success'=> true],201);
    }

    public function update(Request $request, Property $property)
    {
        $validate = Validator::make($request->all(), $this->rules());
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        $property_type = PropertyType::where('type', $request->type)->first();
        $data = collect($validate->validated())->except('images','type')->put('type_id',$property_type->id)->toArray();

        auth()->user()->properties()->update($data);

        if($request->hasFile('images')) {
            $property->images()->delete();
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }

        $this->updatePivot($request,$property,Rule::class, 'rules');
        $this->updatePivot($request,$property, Facility::class,'facilities');
        $this->updatePivot($request,$property,Amenity::class,'amenities');

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

    public function rules()
    {
        return [
            'title' => 'required|min:3|max:100',
            'state' => ['required', VALRule::in(wilayas())],
            'city' => ['required', VALRule::in(communes())],
            'street' => 'required|min:3|max:255',
            'price' => 'required|integer|min:200',
            'type' => 'required|exists:property_types',
            'rooms' => 'required|min:1|integer',
            'bedrooms' => 'required|min:1|integer',
            'bathrooms' => 'required|min:1|integer',
            'beds' => 'required|min:1|integer',
            'images' => 'sometimes|required|max:10',
            'images.*' => 'image|mimes:jpg,bmp,png',
            'rules' => 'sometimes|required',
            'rules.*' => 'exists:rules,name',
            'facilities' => 'sometimes|required',
            'facilities.*' => 'exists:facilities,name',
            'amenities' => 'sometimes|required',
            'amenities.*' => 'exists:amenities,name',
            'description' => 'sometimes|required|max:500',
        ];
    }

    private function updatePivot(Request $request, Property $property, $model,$key) {
        if($request->has($key)) {
            $property->{$key}()->delete();
            foreach ($request->{$key} as $k) {
                $newK = $model::where('name',$k)->first();
                $property->{$key}()->attach($newK->id);
            }
        }
    }
}

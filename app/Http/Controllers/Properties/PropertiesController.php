<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use App\Models\Rule;
use App\Models\TypeOfPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as VALRule;
use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;

class PropertiesController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users','renter.auth'])->except('index','get');
    }

    public function index(Request $request)
    {

        $properties = Property::query();

        foreach ($request->all() as $key=>$val) {
            $allowedKeys = ['title','bedrooms','bathrooms','beds','rooms','type','state','city'];
            if(in_array($key, $allowedKeys)) {
                if($key == 'title') {
                    $properties->where('title','like', "%". $val."%");
                }else if($key == 'type') {
                    $properties->whereHas($key, function($query) use ($request,$val) {
                        $query->where('title', $val)->orWhere('title_fr', $val);
                    });
                }else {
                    if(Property::hasAttribute($key)) {
                        $properties->where($key,$val);
                    }
                }
            }
        }

        $properties = $properties
            ->with(['type:id,title','typeOfPlace:id,title','images:id,url','rules','amenities','facilities'])
            ->paginate( 10);
        foreach ($properties as $property) {
            $property->total_ratings = $property->total_ratings();
            $property->avg_ratings = $property->avg_ratings();
        }
        return response()->json(['success' => true, "message" => $message], $status);
    }

    public function store(Request $request)
    {

        $validate = Validator::make($request->all(), $this->rules());
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        /*
            $wilaya = Wilaya::where('name', $request->state)->first();
            $commune = Commune::where('wilaya_id', $wilaya->id)->where('name', $request->city)->first();

            if(! $commune) {
                return response()->json(['success' => false, 'errors' => "Commune Name doesn't correspond to any Wilaya"],403);
            }
        */
        $property_type = PropertyType::where('title', $request->type)->orWhere('title_fr', $request->type)->first();
        $property_typeOfPlace = TypeOfPlace::where('title', $request->type_of_place)->orWhere('title_fr', $request->type_of_place)->first();

        $data = collect($validate->validated())
            ->put('type_id',$property_type->id)
            ->put('type_of_place_id',$property_typeOfPlace->id)
            ->toArray();

        $user = Renter::find(auth()->id());
        $property = $user->properties()->create($data);

        $this->updatePivot($request,$property,Rule::class, 'rules');
        $this->updatePivot($request,$property, Facility::class,'facilities');
        $this->updatePivot($request,$property,Amenity::class,'amenities');

        if($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/property/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }

        return response()->json(['success'=> true, 'message' => $property],201);
    }

    public function get($id)
    {
        $property = Property::
                    with('images')
                    ->with('amenities')
                    ->with('facilities')
                    ->with('type')
                    ->with('rules')
                    ->with('typeOfPlace')
                    ->where('id', $id)
                    ->first();
        if(is_null($property)) {
            return response()->json(['success' => false, 'errors' => 'No record found'], 403);
        }
        return response()->json(['success' => true, 'data' => $property], 200);
    }

    public function update(Request $request, Property $property)
    {
        $validate = Validator::make($request->all(), $this->rules());
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        $property_type = PropertyType::where('title', $request->type)->orWhere('title_fr', $request->type)->first();
        $property_typeOfPlace = TypeOfPlace::where('title', $request->type_of_place)->orWhere('title_fr', $request->type_of_place)->first();

        $data = collect($validate->validated())
            ->put('type_id',$property_type->id)
            ->put('type_of_place_id',$property_typeOfPlace->id)
            ->toArray();

        $property->fill($data)->save();

        return response()->json(['success' => true, 'message' => $property], 200);
    }

    public function destroy(Property $property)
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
            'state' => 'required',
            'city' => 'required',
            'street' => 'required|min:3|max:255',
            'price' => 'required|integer|min:200',
            'type' => 'required|exists:property_types,title',
            'type_of_place' => 'required|sometimes|exists:type_of_places,title',
            'rooms' => 'required|min:1|integer',
            'bedrooms' => 'required|min:1|integer',
            'bathrooms' => 'required|min:1|integer',
            'beds' => 'required|min:1|integer',
            'long' => 'required|sometimes',
            'lat' => 'required|sometimes',
            'images' => 'sometimes|required|max:10240',
            'images.*' => 'image|mimes:jpg,bmp,png',
            'rules' => 'sometimes|required',
            'rules.*' => 'exists:rules,title',
            'facilities' => 'sometimes|required',
            'facilities.*' => 'exists:facilities,title',
            'amenities' => 'sometimes|required',
            'amenities.*' => 'exists:amenities,title',
            'description' => 'required|max:500',
        ];
    }

    private function updatePivot(Request $request, Property $property, $model,$key) {
        if($request->has($key) && is_array($request->{$key})) {
            $property->{$key}()->delete();
            foreach ($request->{$key} as $k) {
                $newK = $model::where('title',$k)->first();
                $property->{$key}()->attach($newK->id);
            }
        }
    }

}

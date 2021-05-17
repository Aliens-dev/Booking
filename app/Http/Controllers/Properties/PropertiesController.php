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
                        $query->where('type', $val);
                    });
                }else {
                    if(Property::hasAttribute($key)) {
                        $properties->where($key,$val);
                    }
                }
            }
        }

        $properties = $properties->with(['type:id,type','images:id,url'])->paginate( 10);
        foreach ($properties as $property) {
            $property->total_ratings = $property->total_ratings();
            $property->avg_ratings = $property->avg_ratings();
        }
        return response()->json(['data' => $properties]);
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
        $data = collect($validate->validated())->put('type_id',$property_type->id)->toArray();
        $property = auth()->user()->properties()->create($data);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $image_url = "uploads/" . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }
        return response()->json(['success'=> true],201);
    }

    public function get($id)
    {
        $property = Property::
                    with('images')
                    ->with('amenities')
                    ->with('facilities')
                    ->with('type')
                    ->with('rules')
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
        $property_type = PropertyType::where('type', $request->type)->first();
        $data = collect($validate->validated())->put('type_id',$property_type->id)->toArray();

        $property->fill($data)->save();

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
            'state' => ['required', VALRule::in(wilayas())],
            'city' => ['required', VALRule::in(communes())],
            'street' => 'required|min:3|max:255',
            'price' => 'required|integer|min:200',
            'type' => 'required|exists:property_types',
            'rooms' => 'required|min:1|integer',
            'bedrooms' => 'required|min:1|integer',
            'bathrooms' => 'required|min:1|integer',
            'beds' => 'required|min:1|integer',
            'description' => 'sometimes|required|max:500',
        ];
    }

}

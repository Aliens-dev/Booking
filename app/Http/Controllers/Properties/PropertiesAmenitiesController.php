<?php


namespace App\Http\Controllers\Properties;


use App\Http\Controllers\ApiController;
use App\Models\Amenity;
use App\Models\Facility;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertiesAmenitiesController extends ApiController
{
    public function index($propertyId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $amenities = $property->amenities()->get();
        return response()->json(['data' => $amenities, 'success'=> true],200);
    }

    public function store(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $rules = [
            'id' => 'required|exists:amenities,id'
        ];
        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        $property->amenities()->attach($request->id);
        return $this->success('successfully added',201);
    }

    public function destroy($propertyId, $amenityId)
    {
        $property = Property::find($propertyId);
        $amenity = Amenity::find($amenityId);
        if(is_null($property) || is_null($amenity)) {
            return $this->failed("No record found");
        }
        
        $property->amenities()->detach($amenity->id);
        return $this->success("successfully deleted");
    }
}

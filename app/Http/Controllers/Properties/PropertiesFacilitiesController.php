<?php


namespace App\Http\Controllers\Properties;


use App\Http\Controllers\ApiController;
use App\Models\Facility;
use App\Models\Property;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertiesFacilitiesController extends ApiController
{
    public function index($propertyId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $facilities = $property->facilities()->get();
        return response()->json(['data' => $facilities, 'success'=> true],200);
    }

    public function store(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $rules = [
            'id' => 'required|exists:facilities,id'
        ];
        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        $property->facilities()->attach($request->id);
        return $this->success('successfully added',201);
    }

    public function destroy(Request $request, $propertyId, $facilityId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $facility = Facility::find($facilityId);
        if(is_null($facility)) {
            return $this->failed("No record found");
        }
        $property->facilities()->detach($facility->id);
        return $this->success("successfully deleted");
    }
}

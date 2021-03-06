<?php


namespace App\Http\Controllers\Properties;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertiesRulesController extends ApiController
{
    public function index($propertyId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $rules = $property->rules()->get();
        return response()->json(['data' => $rules, 'success'=> true],200);
    }

    public function store(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $rules = [
            'id' => 'required|exists:rules,id'
        ];
        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()], 403);
        }
        $property->rules()->attach($request->id);
        return $this->success('successfully added',201);
    }

    public function destroy(Request $request, $propertyId, $ruleId)
    {
        $property = Property::find($propertyId);
        if(is_null($property)) {
            return $this->failed("No record found");
        }
        $rule = Rule::find($ruleId);
        if(is_null($rule)) {
            return $this->failed("No record found");
        }
        $property->rules()->detach($rule->id);
        return $this->success("successfully deleted");
    }
}

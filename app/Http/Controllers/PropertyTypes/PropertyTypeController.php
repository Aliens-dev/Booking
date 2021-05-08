<?php

namespace App\Http\Controllers\PropertyTypes;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertyTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->except('index');
    }

    public function index()
    {
        $propertyTypes = PropertyType::all();
        return response()->json(['success' => true, 'data' => $propertyTypes], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        PropertyType::create($validate->validated());
        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        $rules = [
            'type' => 'required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        $propertyType->update($validate->validated());
        return response()->json(['success' => true], 200);
    }

    public function destroy(PropertyType $propertyType) {
        $propertyType->delete();
        return response()->json(['success' => true], 200);
    }
}

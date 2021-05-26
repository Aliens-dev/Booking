<?php

namespace App\Http\Controllers\Amenities;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AmenitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->except('index');
    }

    public function index()
    {
        $amenities = Amenity::all();
        return response()->json(['success' => true, 'data' => $amenities], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'sometimes|required|string',
            'title_fr' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'description_fr' => 'sometimes|required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        Amenity::create($validate->validated());
        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, Amenity $amenity)
    {
        $rules = [
            'title' => 'sometimes|required|string',
            'title_fr' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'description_fr' => 'sometimes|required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        $amenity->update($validate->validated());
        return response()->json(['success' => true], 200);
    }

    public function destroy(Amenity $amenity) {
        $amenity->delete();
        return response()->json(['success' => true], 200);
    }
}

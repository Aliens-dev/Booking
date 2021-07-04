<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->except('index');
    }

    public function index()
    {
        $facilities = Facility::all();
        return response()->json(['success' => true, 'data' => $facilities], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'title_fr' => 'sometimes|required|string',
            'description' => 'required|string',
            'description_fr' => 'sometimes|required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        Facility::create($validate->validated());
        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, Facility $facility)
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
        $facility->update($validate->validated());
        return response()->json(['success' => true], 200);
    }

    public function destroy(Facility $facility) {
        $facility->delete();
        return response()->json(['success' => true], 200);
    }
}

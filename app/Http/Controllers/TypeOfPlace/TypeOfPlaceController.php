<?php


namespace App\Http\Controllers\TypeOfPlace;


use App\Http\Controllers\ApiController;
use App\Models\Facility;
use App\Models\TypeOfPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeOfPlaceController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->except('index');
    }

    public function index()
    {
        $typeOfPlace = TypeOfPlace::all();
        return response()->json(['success' => true, 'data' => $typeOfPlace], 200);
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
        TypeOfPlace::create($validate->validated());
        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, $id)
    {
        $typeOfPlace = TypeOfPlace::find($id);
        if(is_null($typeOfPlace)) {
            return $this->failed("No record found");
        }
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
        $typeOfPlace->update($validate->validated());
        return response()->json(['success' => true], 200);
    }

    public function destroy(TypeOfPlace $typeOfPlace) {
        $typeOfPlace->delete();
        return response()->json(['success' => true], 200);
    }
}

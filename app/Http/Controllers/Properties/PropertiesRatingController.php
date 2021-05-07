<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropertiesRatingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:users','client.auth']);
    }

    /**
     * @param Request $request
     * @param Property $property
     * @return JsonResponse
     * */
    public function store(Request $request, Property $property)
    {
        $rules = [
            'rating'=> 'required|integer|min:1|max:5',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false], 403);
        }
        $property->ratings()->create(['rating' => $request->rating]);
        return response()->json(['success' => true], 201);
    }

    public function destroy(Property $property, Rating $rating)
    {
        $rating->delete();
        return response()->json(['success' => true], 200);
    }
}

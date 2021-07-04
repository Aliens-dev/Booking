<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PropertiesRatingController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['auth:users','client.auth']);
    }

    /**
     * @param Request $request
     * @param $propertyId
     * @return JsonResponse
     */
    public function store(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);
        $rules = [
            'rating'=> 'required|integer|min:1|max:5',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false], 403);
        }
        $rating = $property->ratings()->where('client_id', auth()->id())->first();
        if(! is_null($rating)) {
            $rating->rating = $request->rating;
            $rating->save();
            return response()->json(['success' => true], 201); 
        }
        $property->ratings()->create(['rating' => $request->rating,'client_id' => auth()->id()]);
        return response()->json(['success' => true], 201);
    }

    /**
     * @param Request $request
     * @param $propertyId
     * @param $ratingId
     * @return JsonResponse
     */
    public function update(Request $request, $propertyId, $ratingId)
    {
        $rating = Rating::find($ratingId);
        $inspect = Gate::inspect('update', $rating);
        if($inspect->denied()) {
            return $this->failed($inspect->message(), 401);
        }
        $property = Property::find($propertyId);
        $rules = [
            'rating'=> 'required|integer|min:1|max:5',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false], 403);
        }
        $rating = $property
            ->ratings()
            ->where('id', $ratingId)
            ->where('client_id', auth()->id())
            ->update(['rating' => $request->rating]);

        return response()->json(['success' => true], 200);
    }

    /**
     * @param $propertyId
     * @param $ratingId
     * @return JsonResponse
     */
    public function destroy($propertyId, $ratingId)
    {
        $rating = Rating::find($ratingId);
        $inspect = Gate::inspect('delete', $rating);
        if($inspect->denied()) {
            return $this->failed($inspect->message(), 401);
        }
        $property = Property::find($propertyId);
        $property->ratings()->where('id', $ratingId)->delete();
        return response()->json(['success' => true], 200);
    }
}

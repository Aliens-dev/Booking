<?php


namespace App\Http\Controllers\Users;


use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\Property;
use App\Models\Rating;
use App\Models\Renter;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserRatingsController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['auth:users','client.auth']);
    }

    /**
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function store(Request $request, $userId)
    {
        $user = Renter::find($userId)->first();
        $rules = [
            'rating'=> 'required|integer|min:1|max:5',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false], 403);
        }
        $user->ratings()->create(['rating' => $request->rating,'client_id' => auth()->id()]);
        return response()->json(['success' => true], 201);
    }

    /**
     * @param Request $request
     * @param $renterId
     * @param $ratingId
     * @return JsonResponse
     */
    public function update(Request $request, $renterId, $ratingId)
    {
        $rating = Rating::find($ratingId)->first();
        $inspect = Gate::inspect('update', $rating);
        if($inspect->denied()) {
            return $this->failed($inspect->message(), 401);
        }
        $renter = Renter::find($renterId)->first();
        $rules = [
            'rating'=> 'required|integer|min:1|max:5',
        ];
        $validate = Validator::make($request->all(), $rules);
        if($validate->fails()) {
            return response()->json(['success' => false], 403);
        }
        $renter->ratings()
            ->where('id', $ratingId)
            ->where('client_id',auth()->id())
            ->update(['rating' => $request->rating]);

        return response()->json(['success' => true], 200);
    }

    /**
     * @param $userId
     * @param $ratingId
     * @return JsonResponse
     */
    public function destroy($userId, $ratingId)
    {
        $rating = Rating::find($ratingId)->first();
        $inspect = Gate::inspect('delete', $rating);
        if($inspect->denied()) {
            return $this->failed($inspect->message(), 401);
        }
        $user = Renter::find($userId)->first();
        $user->ratings()->where('id', $ratingId)->delete();
        return response()->json(['success' => true], 200);
    }
}

<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class PropertyImagesController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:users', 'renter.auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Property $property
     * @return JsonResponse
     */
    public function index(Property $property)
    {
        $images = $property->images()->get();
        return response()->json(['success' => true,'data'=> $images], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Property $property
     * @return JsonResponse
     */
    public function store(Request $request, Property $property)
    {
        $inspect = Gate::inspect('create', $property);
        if($inspect->denied()) {
            return $this->failed("Not allowed to add images", 401);
        }
        $rules = [
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,bmp,png',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false,'errors' => $validate->errors()], 403);
        }
        if($request->hasFile('images')) {

            $property->images()->delete();
            $images = $request->images;
            if(is_array($images)) {
                foreach ($images as $image) {
                    $image_url = 'uploads/property/' . $image->store($property->id);
                    $property->images()->create(['url' => $image_url]);
                }
            }else {
                $image_url = 'uploads/property/' . $images->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }

        }
        return $this->success();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Property $property
     * @return JsonResponse
     */
    public function update(Request $request, Property $property)
    {
        $inspect = Gate::inspect('update', $property);
        if($inspect->denied()) {
            return response()->json(['success' => false], 403);
        }
        $rules = [
            'images' => 'required|max:10',
            'images.*' => 'image|mimes:jpg,bmp,png',
        ];

        $validate = Validator::make($request->all(), $rules);

        if($validate->fails()) {
            return response()->json(['success' => false,'errors' => $validate->errors()], 403);
        }
        if($request->hasFile('images')) {
            $property->images()->delete();
            $images = $request->images;
            foreach ($images as $image) {
                $image_url = 'uploads/' . $image->store($property->id);
                $property->images()->create(['url' => $image_url]);
            }
        }
        return response()->json(['success' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Property $property
     * @return JsonResponse
     */
    public function destroy(Property $property)
    {
        $inspect = Gate::inspect('update', $property);
        if($inspect->denied()) {
            return response()->json(['success' => false], 403);
        }
        $property->images()->delete();
        return response()->json(['success' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Property $property
     * @param Image $image
     * @return JsonResponse
     */
    public function destroySingle(Property $property, Image $image)
    {
        if(! $property->images->contains($image)) {
            return response()->json(['success' => false], 403);
        }
        $inspect = Gate::inspect('update', $property);
        if($inspect->denied()) {
            return response()->json(['success' => false], 403);
        }
        $image->delete();
        return response()->json(['success' => true], 200);
    }
}

<?php


namespace App\Http\Controllers\Properties;


use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertiesRulesController extends Controller
{
    public function index(Property $property)
    {
        $rules = $property->rules()->get();
        return response()->json(['data' => $rules, 'success'=> true],200);
    }
}

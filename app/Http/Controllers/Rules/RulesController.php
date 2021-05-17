<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RulesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth'])->except('index');
    }

    public function index()
    {
        $rules = Rule::all();
        return response()->json(['success' => true, 'data' => $rules], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'sometimes|required|string',
            'title_ar' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'description_ar' => 'sometimes|required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        Rule::create($validate->validated());
        return response()->json(['success' => true], 201);
    }

    public function update(Request $request, Rule $rule)
    {
        $rules = [
            'title' => 'sometimes|required|string',
            'title_ar' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'description_ar' => 'sometimes|required|string'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json(['success' => false], 403);
        }
        $rule->update($validate->validated());
        return response()->json(['success' => true], 200);
    }

    public function destroy(Rule $rule) {
        $rule->delete();
        return response()->json(['success' => true], 200);
    }
}

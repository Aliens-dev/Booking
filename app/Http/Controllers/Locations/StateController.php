<?php


namespace App\Http\Controllers\Locations;


use App\Http\Controllers\ApiController;
use Kossa\AlgerianCities\Wilaya;

class StateController extends ApiController
{

    public function index()
    {
        $states = Wilaya::all();
        return $this->success($states);
    }
}

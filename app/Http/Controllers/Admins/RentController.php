<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:users','admin.auth']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $rent = Reservation::paginate(10);
        return response()->json(['success' => true, 'data' => $rent], 200);
    }
}

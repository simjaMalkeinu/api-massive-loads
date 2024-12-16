<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function getPersonas(Request $request) {
        $personas = DB::table("data_view")->paginate(100);

        return response()->json($personas);

    }
}

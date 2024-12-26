<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function info(Request  $request)
    {
        $affected = DB::table('personas')
            ->where('id', $request->id)
            ->update(['nombre' => $request->nombre, 'paterno' => $request->paterno, 'materno' => $request->materno]);

        return response()->json(['status' => 200, 'message' => 'Nombre actualizado', 'result' => $affected]);
    }

    public function telefono(Request  $request)
    {
        $affected = DB::table('telefonos')
            ->where('id', $request->id)
            ->update(['numero_telefono' => $request->numero_telefono]);

        return response()->json(['status' => 200, 'message' => 'Telefono actualizado', 'result' => $affected]);
    }

    public function direccion(Request  $request)
    {
        $affected = DB::table('direcciones')
            ->where('id', $request->id)
            ->update(['calle' => $request->calle, 'numero_exterior' => $request->numero_exterior, 'numero_interior' => $request->numero_interior, 'colonia' => $request->colonia, 'cp' => $request->cp]);

        return response()->json(['status' => 200, 'message' => 'Direccion actualizada', 'result' => $affected]);
    }
}

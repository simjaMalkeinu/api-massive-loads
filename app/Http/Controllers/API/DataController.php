<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function getPersonas(Request $request)
    {
        $personas = DB::table("data_view")->paginate(100);

        // Convertir los teléfonos y direcciones a arrays
        $personas->getCollection()->transform(function ($persona) {
            $persona->telefonos = explode(',', $persona->telefonos); // Divide los teléfonos en un array
            $persona->direcciones = explode("\r", $persona->direcciones); // Divide las direcciones en un array
            return $persona;
        });

        // Regresa la respuesta con paginación
        return response()->json($personas);
    }

    public function getPersonaById(Request $request, $id)
    {
        $persona = DB::table("personas")->where('id', $id)->first();
        $telefonos = DB::table("telefonos")->where('persona_id', $id)->get();
        $direcciones = DB::table("direcciones")->where('persona_id', $id)->get();
        return response()->json(['persona' => $persona, 'telefonos' => $telefonos, 'direcciones' => $direcciones]);
    }
}

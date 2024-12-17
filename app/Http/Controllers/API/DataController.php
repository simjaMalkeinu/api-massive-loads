<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function getPersonas(Request $request) {
        $personas = DB::table("data_view")->paginate(100);
    
        // Convertir los teléfonos y direcciones a arrays
        $personas->getCollection()->transform(function($persona) {
            $persona->telefonos = explode(',', $persona->telefonos); // Divide los teléfonos en un array
            $persona->direcciones = explode("\r", $persona->direcciones); // Divide las direcciones en un array
    
            return $persona;
        });
    
        // Regresa la respuesta con paginación
        return response()->json($personas);
    }
    
}

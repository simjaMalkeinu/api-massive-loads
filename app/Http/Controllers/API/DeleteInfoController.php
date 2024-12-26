<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteInfoController extends Controller
{
    public function deletePersonById(Request $request, $id)
    {
        $deleted = DB::delete('DELETE FROM personas WHERE id = ?', [$id]);

        // Retorna una respuesta indicando el resultado de la operación
        if ($deleted) {
            return response()->json(['status' => 200, 'message' => 'Persona eliminada correctamente', 'result' => $deleted]);
        } else {
            return response()->json(['status' => 404, 'message' => 'No se encontro a la persona', 'result' => $deleted]);
        }
    }
}

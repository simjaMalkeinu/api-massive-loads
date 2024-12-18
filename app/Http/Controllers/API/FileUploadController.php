<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ImportCsvData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileUploadController extends Controller
{

    public function upload(Request $request)
    {
        // Validar que el archivo 'file' esté presente
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB máximo
        ]);

        // Obtener el archivo y moverlo a la carpeta temporal del sistema
        $file = $request->file('file');
        
        $tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'uploaded_file.csv';

        // Mover el archivo a la carpeta temporal
        $file->move(sys_get_temp_dir(), 'uploaded_file.csv');

        // var_dump($tempFilePath);
        $routeFile = str_replace('\\', '/', $tempFilePath);

        // Ejecutar el comando LOAD DATA LOCAL INFILE
        try {
            DB::connection()->getPdo()->exec("
                LOAD DATA LOCAL INFILE '{$routeFile}' 
                INTO TABLE tmp_data 
                FIELDS TERMINATED BY ',' 
                ENCLOSED BY '\"' 
                LINES TERMINATED BY '\n' 
                IGNORE 1 LINES
                (nombre, paterno, materno, telefono, calle, numero_exterior, numero_interior, colonia, cp);
            ");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => 500, 'message' => 'Ocurrio un error al guardar el archivo en el servidor'], 500);
        } finally {
            // Eliminar el archivo temporal después de procesarlo
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }

        try {
            DB::connection()->getPdo()->exec(("CALL MigrarDatosPersonas();"));

            return response()->json(['status' => 200, 'message' => 'Archivo CSV cargado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => 200, 'message' => 'Error al migrar la informacion'], 500);
        }
    }
}

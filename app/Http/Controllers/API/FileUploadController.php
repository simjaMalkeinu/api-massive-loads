<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ImportCsvData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    // public function upload(Request $request)
    // {
    //     // Validar si el archivo es Excel o CSV
    //     $validator = Validator::make($request->all(), [
    //         "file" => "required|file|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain|max:10240",
    //     ], [
    //         "file.mimes" => "El archivo debe ser un Excel (.xls o .xlsx) o un CSV.",
    //         "file.max"   => "El archivo no debe superar los 10 MB.",
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             "error" => $validator->errors()->first(),
    //         ], 400);
    //     }

    //     $file = $request->file("file");

    //     if ($file->isValid()) {
    //         // Guardar el archivo temporalmente
    //         $filePath = $file->storeAs('uploads', $file->getClientOriginalName());

    //         // Verificar si el archivo existe en el almacenamiento
    //         if (!Storage::exists('/uploads/' . basename($filePath))) {
    //             return response()->json([
    //                 "status" => "error",
    //                 "message" => "Error al guardar el archivo. Intenta nuevamente.",
    //             ], 500);
    //         }

    //         // Construir la ruta completa del archivo
    //         $csvFilePath = storage_path("app/private/uploads/" . basename($filePath));
    //         // Reemplazar separadores de Windows (\) por los estándar de Unix (/)
    //         $absoluteCsvPath = str_replace('\\', '/', $csvFilePath);

    //         var_dump($absoluteCsvPath);

    //         // Verificar si el archivo realmente existe antes de continuar
    //         if (!file_exists($absoluteCsvPath)) {
    //             return response()->json([
    //                 "status" => "error",
    //                 "message" => "El archivo no se pudo guardar correctamente.",
    //             ], 500);
    //         }

    //         // Si el archivo existe, proceder con la importación
    //         try {
    //             // Importar el CSV a la base de datos usando LOAD DATA LOCAL INFILE
    //             DB::connection()->getPdo()->exec("
    //                 LOAD DATA INFILE '{$absoluteCsvPath}'
    //                 INTO TABLE tmp_data
    //                 FIELDS TERMINATED BY ',' 
    //                 ENCLOSED BY '\"' 
    //                 LINES TERMINATED BY '\n'
    //                 IGNORE 1 ROWS");
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 "status" => "error",
    //                 "message" => "Error al importar los datos: " . $e->getMessage(),
    //             ], 500);
    //         }

    //         // Eliminar el archivo temporal
    //         unlink($csvFilePath);

    //         return response()->json([
    //             "status" => 200,
    //             "message" => "Archivo importado correctamente a la base de datos",
    //         ]);
    //     }
    // }

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
                IGNORE 1 LINES;
            ");
            return response()->json(['status' => 200, 'message' => 'Archivo CSV cargado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } finally {
            // Eliminar el archivo temporal después de procesarlo
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }
}

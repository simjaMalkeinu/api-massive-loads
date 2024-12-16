<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "file" => "required|file|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:10240",
        ], [
            "file.mimes" => "El archivo debe ser un Excel con formato .xls o .xlsx.",
            "file.max"   => "El archivo no debe superar los 10 MB.",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()->first(),
            ], 0);
        }

        $file = $request->file("file");
        if ($file->isValid()) {
            $fileName = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('uploads', $fileName);
            return response()->json([
                "status" => 200,
                "message" => "Archivo guardado correctamente"
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => ""
            ]);
        }
    }
}

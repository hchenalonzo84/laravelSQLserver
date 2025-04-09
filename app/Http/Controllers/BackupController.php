<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BackupController extends Controller
{
    public function ejecutar()
    {
        $rutaBat = 'E:\CopiasSQL\seguridad.bat';
        $carpetaRespaldo = 'E:\CopiasSQL';

        // Validar existencia del .bat
        if (!file_exists($rutaBat)) {
            return back()->withErrors(['backup' => 'El archivo .bat no fue encontrado.']);
        }

        try {
            // Ejecutar el .bat
            pclose(popen("start /B \"\" \"$rutaBat\"", "r"));

            // Esperar unos segundos a que el archivo se genere
            sleep(1); // ajusta si el respaldo tarda más

            // Buscar el archivo .bak más reciente
            $archivos = glob($carpetaRespaldo . '\*.bak');
            $archivoReciente = collect($archivos)
                ->mapWithKeys(fn($archivo) => [$archivo => filemtime($archivo)])
                ->sortDesc()
                ->keys()
                ->first();

            if ($archivoReciente) {
                $nombreArchivo = basename($archivoReciente);
                return back()->with('success', "Respaldo creado correctamente: $nombreArchivo");
            } else {
                return back()->withErrors(['backup' => 'No se encontró ningún archivo .bak en la carpeta.']);
            }

        } catch (\Exception $e) {
            return back()->withErrors(['backup' => 'Error al ejecutar el respaldo: ' . $e->getMessage()]);
        }
    }
}

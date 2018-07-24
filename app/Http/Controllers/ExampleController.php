<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Log;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    const ID_INTERNET = 5;

    const ID_REVISTA = 4;

    const ID_PERIODICO = 3;

    const ID_RADIO = 2;

    const ID_TELEVISION = 1;

    public function __construct()
    {
        //
    }

    public function deteleCartones(Request $req)
    {
        $fechaIni = $req->query('fecha_inicio');
        $fechaFin = $req->query('fecha_fin');
        $counts = new \stdClass();
        $counts->noticias = 0;
        $counts->adjuntoTV = 0;
        $counts->adjuntoRD = 0;
        $counts->adjuntoRE = 0;
        $counts->adjuntoPE = 0;
        $counts->adjuntoIN = 0;
        $counts->deletedFiles = 0;
        $counts->deletedFilesTV = 0;
        $counts->deletedFilesRD = 0;
        $counts->deletedFilesRE = 0;
        $counts->deletedFilesPE = 0;
        $counts->deletedFilesIN = 0;
        $counts->filesNotExist = 0;
        $counts->filesNotExistTV = 0;
        $counts->filesNotExistRD = 0;
        $counts->filesNotExistRE = 0;
        $counts->filesNotExistPE = 0;
        $counts->filesNotExistIN = 0;

        // contador cartones
        $counts->cartones = 0;
        $counts->deletedFilesCartones = 0;
        $counts->filesNotExistCartones = 0;

        // contador columnas financieras
        $counts->columnasFinancieras = 0;
        $counts->deletedFilesColFin = 0;
        $counts->filesNotExistColFin = 0;

        // contador columnas financieras
        $counts->columnasPoliticas = 0;
        $counts->deletedFilesColPol = 0;
        $counts->filesNotExistColPol = 0;


        // contador primeras planas
        $counts->primerasPlanas = 0;
        $counts->deletedFilesPrimeraPlana = 0;
        $counts->filesNotExistPrimeraPlana = 0;

        // Log::info("Obteniendo las noticias entre {$fechaIni} y {$fechaFin}");
        // $noticias = DB::table('noticia')
        //     ->select('id_noticia', 'id_tipo_fuente as fuente') 
        //     ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
        //     ->get();
        
        // $counts->noticias = $noticias->count();
        // Log::info("Numero de noticias: {$counts->noticias}");
        // Log::info("Resultado: {$noticias}");
        // Log::info('Ordenando noticias por fuente...');
        // $groupBySource = array();
        // foreach ($noticias as $noticia) {
        //     $groupBySource[$noticia->fuente][] = $noticia->id_noticia;
        // }

        // Log::info('Iniciando el borrado de archivos de noticias.');
        // foreach ($groupBySource as $key => $idNoticias) {
        //     if ($key == self::ID_TELEVISION) {
        //         Log::info('Buscando archivos de televisión en la base de datos...');
        //         $adjuntosTV = DB::table('adjunto')
        //             ->select('nombre_archivo')
        //             ->whereIn('id_noticia', $idNoticias)
        //             ->get();
        //         $counts->adjuntoTV = $adjuntosTV->count();
        //         Log::info("Se encontraron {$counts->adjuntoTV} coincidencias en la base de datos");
        //         if($counts->adjuntoTV > 0) {
        //             Log::info('Validando si existen los archivos');
        //             foreach ($adjuntosTV as $adjuntoTV) {
        //                 $filePath = env('PATH_MEDIA_TELEVISION') . $adjuntoTV->nombre_archivo;
        //                 if (file_exists($filePath)) {
        //                     if(unlink($filePath)) {
        //                         $counts->deletedFiles++;
        //                         $counts->deletedFilesTV++;
        //                         Log::info("Se ha borrado el archivo {$filePath}");
        //                     }
        //                 }else {
        //                     $counts->filesNotExistTV++;
        //                     $counts->filesNotExist++;
        //                     Log::info("El archivo {$filePath} no existe");
        //                 }
        //             }
        //         } else {
        //             $counts->deletedFilesTV = 0;
        //             Log::info('No hay archivos de televisión para borrar');
        //         }
        //     }

        //     if ($key == self::ID_RADIO) {
        //         Log::info('Buscando archivos de radio en la base de datos...');
        //         $adjuntosRD = DB::table('adjunto')
        //             ->select('nombre_archivo')
        //             ->whereIn('id_noticia', $idNoticias)
        //             ->get();
        //         $counts->adjuntoRD = $adjuntosRD->count();
        //         Log::info("Se encontraron {$counts->adjuntoRD} coincidencias en la base de datos");
        //         if($counts->adjuntoRD > 0) {
        //             Log::info('Validando si existen los archivos');
        //             foreach ($adjuntosRD as $adjuntoRD) {
        //                 $filePath = env('PATH_MEDIA_RADIO') . $adjuntoRD->nombre_archivo;
        //                 if (file_exists($filePath)) {
        //                     if(unlink($filePath)) {
        //                         $counts->deletedFiles++;
        //                         $counts->deletedFilesRD++;
        //                         Log::info("Se ha borrado el archivo {$filePath}");
        //                     }
        //                 }else {
        //                     $counts->filesNotExistRD++;
        //                     $counts->filesNotExist++;
        //                     Log::info("El archivo {$filePath} no existe");
        //                 }
        //             }
        //         } else {
        //             $counts->deletedFilesRD = 0;
        //             Log::info('No hay archivos de radio para borrar');
        //         }
        //     }

        //     if ($key == self::ID_PERIODICO) {
        //         Log::info('Buscando archivos de periodicos en la base de datos...');
        //         $adjuntosPE = DB::table('adjunto')
        //             ->select('nombre_archivo')
        //             ->whereIn('id_noticia', $idNoticias)
        //             ->get();
        //         $counts->adjuntoPE = $adjuntosPE->count();
        //         Log::info("Se encontraron {$counts->adjuntoPE} coincidencias en la base de datos");
        //         if($counts->adjuntoPE > 0) {
        //             Log::info('Validando si existen los archivos');
        //             foreach ($adjuntosPE as $adjuntoPE) {
        //                 $filePath = env('PATH_MEDIA_PERIODICO') . $adjuntoPE->nombre_archivo;
        //                 if (file_exists($filePath)) {
        //                     if(unlink($filePath)) {
        //                         $counts->deletedFiles++;
        //                         $counts->deletedFilesPE++;
        //                         Log::info("Se ha borrado el archivo {$filePath}");
        //                     }
        //                 }else {
        //                     $counts->filesNotExistPE++;
        //                     $counts->filesNotExist++;
        //                     Log::info("El archivo {$filePath} no existe");
        //                 }
        //             }
        //         } else {
        //             $counts->deletedFilesPE = 0;
        //             Log::info('No hay archivos de periodico para borrar');
        //         }
        //     }

        //     if ($key == self::ID_REVISTA) {
        //         Log::info('Buscando archivos de revista en la base de datos...');
        //         $adjuntosRE = DB::table('adjunto')
        //             ->select('nombre_archivo')
        //             ->whereIn('id_noticia', $idNoticias)
        //             ->get();
        //         $counts->adjuntoRE = $adjuntosRE->count();
        //         Log::info("Se encontraron {$counts->adjuntoRE} coincidencias en la base de datos");
        //         if($counts->adjuntoRE > 0) {
        //             Log::info('Validando si existen los archivos');
        //             foreach ($adjuntosRE as $adjuntoRE) {
        //                 $filePath = env('PATH_MEDIA_REVISTA') . $adjuntoRE->nombre_archivo;
        //                 if (file_exists($filePath)) {
        //                     if(unlink($filePath)) {
        //                         $counts->deletedFiles++;
        //                         $counts->deletedFilesRE++;
        //                         Log::info("Se ha borrado el archivo {$filePath}");
        //                     }
        //                 }else {
        //                     $counts->filesNotExistRE++;
        //                     $counts->filesNotExist++;
        //                     Log::info("El archivo {$filePath} no existe");
        //                 }
        //             }
        //         } else {
        //             $counts->deletedFilesRE = 0;
        //             Log::info('No hay archivos de revistas para borrar');
        //         }
        //     }

        //     if ($key == self::ID_INTERNET) {
        //         Log::info('Buscando archivos de internet en la base de datos...');
        //         $adjuntosIN = DB::table('adjunto')
        //             ->select('nombre_archivo')
        //             ->whereIn('id_noticia', $idNoticias)
        //             ->get();
        //         $counts->adjuntoIN = $adjuntosIN->count();
        //         Log::info("Se encontraron {$counts->adjuntoIN} coincidencias en la base de datos");
        //         if($counts->adjuntoIN > 0) {
        //             Log::info('Validando si existen los archivos');
        //             foreach ($adjuntosIN as $adjuntoIN) {
        //                 $filePath = env('PATH_MEDIA_INTERNET') . $adjuntoIN->nombre_archivo;
        //                 if (file_exists($filePath)) {
        //                     if(unlink($filePath)) {
        //                         $counts->deletedFiles++;
        //                         $counts->deletedFilesIN++;
        //                         Log::info("Se ha borrado el archivo {$filePath}");
        //                     }
        //                 }else {
        //                     $counts->filesNotExistIN++;
        //                     $counts->filesNotExist++;
        //                     Log::info("El archivo {$filePath} no existe");
        //                 }
        //             }
        //         } else {
        //             $counts->deletedFilesIN = 0;
        //             Log::info('No hay archivos de internet para borrar');
        //         }
        //     }
        // }

        //eliminando cartones
        Log::info("Obteniendo los cartones entre {$fechaIni} y {$fechaFin}");
        $cartones = DB::table('carton')
            ->select('imagen')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->cartones = $cartones->count();
        Log::info("Numero de cartones: {$counts->cartones}");
        Log::info("Resultado: {$cartones}");
        Log::info('Iniciando el borrado de archivos de cartones.');
        Log::info('Validando si existen los archivos');
        if($counts->cartones > 0) {
            foreach ($cartones as $objCarton) {
                $filePath = env('PATH_MEDIA_CARTONES') . $objCarton->imagen;
                if (file_exists($filePath)) {
                    if(unlink($filePath)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesCartones++;
                        Log::info("Se ha borrado el archivo {$filePath}");
                    }
                }else {
                    $counts->filesNotExistCartones++;
                    $counts->filesNotExist++;
                    Log::info("El archivo {$filePath} no existe");
                }
            }
        } else {
            $counts->deletedFilesCartones = 0;
            Log::info('No hay archivos de cartones para borrar');
        }

        //eliminando columnas financieras
        Log::info("Obteniendo las columnas financieras entre {$fechaIni} y {$fechaFin}");
        $colFinancieras = DB::table('columna_financiera')
            ->select('imagen_jpg', 'archivo_pdf')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->columnasFinancieras = $colFinancieras->count();
        Log::info("Numero de columnas financieras: {$counts->columnasFinancieras}");
        Log::info("Resultado: {$colFinancieras}");
        Log::info('Iniciando el borrado de archivos de columnas financieras.');
        Log::info('Validando si existen los archivos');
        if($counts->columnasFinancieras > 0) {
            foreach ($colFinancieras as $colFinanciera) {
                $filePathImagen = env('PATH_MEDIA_COL_FINANCIERAS') . $colFinanciera->imagen_jpg;
                if (file_exists($filePathImagen)) {
                    if(unlink($filePathImagen)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColFin++;
                        Log::info("Se ha borrado el archivo {$filePathImagen}");
                    }
                }else {
                    $counts->filesNotExistColFin++;
                    $counts->filesNotExist++;
                    Log::info("El archivo {$filePathImagen} no existe");
                }
                $filePathDoc = env('PATH_MEDIA_COL_FINANCIERAS') . $colFinanciera->archivo_pdf;
                if (file_exists($filePathDoc)) {
                    if(unlink($filePathDoc)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColFin++;
                        Log::info("Se ha borrado el archivo {$filePathDoc}");
                    }
                }else {
                    $counts->filesNotExistColFin++;
                    $counts->filesNotExist++;
                    Log::info("El archivo {$filePathDoc} no existe");
                }
            }
        } else {
            $counts->columnasFinancieras = 0;
            Log::info('No hay archivos de columnas financieras para borrar');
        }

        //eliminando columnas politicas
        Log::info("Obteniendo las columnas politicas entre {$fechaIni} y {$fechaFin}");
        $colPoliticas = DB::table('columna_politica')
            ->select('imagen_jpg', 'archivo_pdf')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->columnasPoliticas = $colPoliticas->count();
        Log::info("Numero de columnas politicas: {$counts->columnasPoliticas}");
        Log::info("Resultado: {$colPoliticas}");
        Log::info('Iniciando el borrado de archivos de columnas politicas.');
        Log::info('Validando si existen los archivos');
        if($counts->columnasPoliticas > 0) {
            foreach ($colPoliticas as $colPolitica) {
                $filePathImagen = env('PATH_MEDIA_COL_POLITICAS') . $colPolitica->imagen_jpg;
                if (file_exists($filePathImagen)) {
                    if(unlink($filePathImagen)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColPol++;
                        Log::info("Se ha borrado el archivo {$filePathImagen}");
                    }
                }else {
                    $counts->filesNotExistColPol++;
                    $counts->filesNotExist++;
                    Log::info("El archivo {$filePathImagen} no existe");
                }
                $filePathDoc = env('PATH_MEDIA_COL_POLITICAS') . $colPolitica->archivo_pdf;
                if (file_exists($filePathDoc)) {
                    if(unlink($filePathDoc)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColPol++;
                        Log::info("Se ha borrado el archivo {$filePathDoc}");
                    }
                }else {
                    $counts->filesNotExistColPol++;
                    $counts->filesNotExist++;
                    Log::info("El archivo {$filePathDoc} no existe");
                }
            }
        } else {
            $counts->columnasPoliticas = 0;
            Log::info('No hay archivos de columnas politicas para borrar');
        }

        //eliminando primeras planas
        Log::info("Obteniendo las primeras planas entre {$fechaIni} y {$fechaFin}");
        $primerasPlanas = DB::table('primera_plana')
            ->select('imagen')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->primerasPlanas = $primerasPlanas->count();
        Log::info("Numero de primeras planas: {$counts->primerasPlanas}");
        Log::info("Resultado: {$primerasPlanas}");
        Log::info('Iniciando el borrado de archivos de primeras planas.');
        Log::info('Validando si existen los archivos');
        if($counts->primerasPlanas > 0) {
            foreach ($primerasPlanas as $objPrimeraPlana) {
                $filePath = env('PATH_MEDIA_PRIMERAS_PLANAS') . $objPrimeraPlana->imagen;
                if (file_exists($filePath)) {
                    if(unlink($filePath)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesPrimeraPlana++;
                        Log::info("Se ha borrado el archivo {$filePath}");
                    }
                }else {
                    $counts->filesNotExistPrimeraPlana++;
                    $counts->filesNotExist++;
                    Log::info("El archivo {$filePath} no existe");
                }
            }
        } else {
            $counts->deletedFilesPrimerasPlanas = 0;
            Log::info('No hay archivos de primeras planas para borrar');
        }

        return response()->json(['reporte de noticias:' => $counts]);

    }
}

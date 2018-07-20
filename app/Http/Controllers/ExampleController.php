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

        Log::info("Obteniendo las noticias entre {$fechaIni} y {$fechaFin}");
        $noticias = DB::table('noticia')
            ->select('id_noticia', 'id_tipo_fuente as fuente') 
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();
        
        $counts->noticias = $noticias->count();
        Log::info("Numero de noticias: {$counts->noticias}");
        Log::info("Resultado: {$noticias}");
        Log::info('Ordenando noticias por fuente...');
        $groupBySource = array();
        foreach ($noticias as $noticia) {
            $groupBySource[$noticia->fuente][] = $noticia->id_noticia;
        }

        Log::info('Iniciando el borrado de archivos de noticias.');
        foreach ($groupBySource as $key => $idNoticias) {
            if ($key == self::ID_TELEVISION) {
                Log::info('Buscando archivos de televisión en la base de datos...');
                $adjuntosTV = DB::table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoTV = $adjuntosTV->count();
                Log::info("Se encontraron {$counts->adjuntoTV} coincidencias en la base de datos");
                if($counts->adjuntoTV > 0) {
                    Log::info('Validando si existen los archivos');
                    foreach ($adjuntosTV as $adjuntoTV) {
                        $filePath = env('PATH_MEDIA_TELEVISION') . $adjuntoTV->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesTV++;
                                Log::info("Se ha borrado el archivo {$filePath}");
                            }
                            // else{
                            //     $counts->fileNotFound++;
                            //     $counts->fileNotFoundTV++;
                            //     Log::info("No se ha borrado el archivo {$filePath}");
                            // }
                        }else {
                            $counts->filesNotExistTV++;
                            $counts->filesNotExist++;
                            Log::info("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesTV = 0;
                    Log::info('No hay archivos de televisión para borrar');
                }
            }

            if ($key == self::ID_RADIO) {
                Log::info('Buscando archivos de radio en la base de datos...');
                $adjuntosRD = DB::table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoRD = $adjuntosRD->count();
                Log::info("Se encontraron {$counts->adjuntoRD} coincidencias en la base de datos");
                if($counts->adjuntoRD > 0) {
                    Log::info('Validando si existen los archivos');
                    foreach ($adjuntosRD as $adjuntoRD) {
                        $filePath = env('PATH_MEDIA_RADIO') . $adjuntoRD->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesRD++;
                                Log::info("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistRD++;
                            $counts->filesNotExist++;
                            Log::info("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesRD = 0;
                    Log::info('No hay archivos de radio para borrar');
                }
            }

            if ($key == self::ID_PERIODICO) {
                Log::info('Buscando archivos de periodicos en la base de datos...');
                $adjuntosPE = DB::table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoPE = $adjuntosPE->count();
                Log::info("Se encontraron {$counts->adjuntoPE} coincidencias en la base de datos");
                if($counts->adjuntoPE > 0) {
                    Log::info('Validando si existen los archivos');
                    foreach ($adjuntosPE as $adjuntoPE) {
                        $filePath = env('PATH_MEDIA_PERIODICO') . $adjuntoPE->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesPE++;
                                Log::info("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistPE++;
                            $counts->filesNotExist++;
                            Log::info("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesPE = 0;
                    Log::info('No hay archivos de periodico para borrar');
                }
            }

            if ($key == self::ID_REVISTA) {
                Log::info('Buscando archivos de revista en la base de datos...');
                $adjuntosRE = DB::table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoRE = $adjuntosRE->count();
                Log::info("Se encontraron {$counts->adjuntoRE} coincidencias en la base de datos");
                if($counts->adjuntoRE > 0) {
                    Log::info('Validando si existen los archivos');
                    foreach ($adjuntosRE as $adjuntoRE) {
                        $filePath = env('PATH_MEDIA_REVISTA') . $adjuntoRE->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesRE++;
                                Log::info("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistRE++;
                            $counts->filesNotExist++;
                            Log::info("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesRE = 0;
                    Log::info('No hay archivos de revistas para borrar');
                }
            }

            if ($key == self::ID_INTERNET) {
                Log::info('Buscando archivos de internet en la base de datos...');
                $adjuntosIN = DB::table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoIN = $adjuntosIN->count();
                Log::info("Se encontraron {$counts->adjuntoIN} coincidencias en la base de datos");
                if($counts->adjuntoIN > 0) {
                    Log::info('Validando si existen los archivos');
                    foreach ($adjuntosIN as $adjuntoIN) {
                        $filePath = env('PATH_MEDIA_INTERNET') . $adjuntoIN->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesIN++;
                                Log::info("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistIN++;
                            $counts->filesNotExist++;
                            Log::info("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesIN = 0;
                    Log::info('No hay archivos de internet para borrar');
                }
            }
        }
        return response()->json(['reporte de noticias:' => $counts]);
    }
}

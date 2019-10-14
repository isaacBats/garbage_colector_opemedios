<?php

namespace App\Http\Controllers;

use App\Adjunto;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\TipoFuenteController;
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

    private $tipoFuenteController;

    private $noticiaController;

    private $tiposFuente;

    public function __construct( TipoFuenteController $tipoFuenteController, NoticiaController $noticiaController )
    {
        $this->tipoFuenteController = $tipoFuenteController;
        $this->noticiaController = $noticiaController;

        $this->linfo('Obteniendo Tipos de Fuente Actuales');
        $this->tiposFuente = $this->tipoFuenteController->getAllFontTypes();
        $this->linfo("Las Fuentes actuales son: {$this->tiposFuente}");
    }

    private function setup () {

        $counts = new \stdClass();
        $counts->noticias = 0;
        foreach ($this->tiposFuente as $tipos) {
            $adjunto = "adjunto{$tipos['sigla']}";
            $noticia = "noticia{$tipos['sigla']}";
            $delete = "deletedFiles{$tipos['sigla']}";
            $filesNotExist = "filesNotExist{$tipos['sigla']}";
            
            $counts->$adjunto = 0;
            $counts->$noticia = 0;
            $counts->$delete = 0;
            $counts->$filesNotExist = 0;
        }
        $counts->deletedFiles = 0;
        $counts->filesNotExist = 0;
        
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

        return $counts;

    }

    public function deteleCartones(Request $req)
    {
        $fechaIni = $req->query('fecha_inicio');
        $fechaFin = $req->query('fecha_fin');

        $counts = $this->setup();
        
        $this->linfo("Obteniendo las noticias entre {$fechaIni} y {$fechaFin}");
        $noticias = $this->noticiaController->getNewsin($fechaIni, $fechaFin);
        
        $counts->noticias = $noticias->count();
        $this->linfo("Numero de noticias: {$counts->noticias}");
        $this->linfo("Resultado: {$noticias}");
        $this->linfo('Ordenando noticias por fuente...');
        $groupBySource = array();
        foreach ($noticias as $noticia) {
            $groupBySource[$noticia->fuente][] = $noticia->id_noticia;
        }

        $this->linfo('Obteniendo Tipos de Fuente Actuales');
        $tiposFuente = $this->tipoFuenteController->getAllFontTypes();
        $this->linfo("Las Fuentes actuales son: {$tiposFuente}");

        $this->linfo('Iniciando el borrado de archivos de noticias.');
        foreach ($groupBySource as $key => $idNoticias) {
            // $this->deleteByType($tiposFuente, $idNoticias);
            if ($key == self::ID_TELEVISION) {
                $this->linfo('Buscando archivos de televisión en la base de datos...');
                $adjuntosTV = DB::connection('mysql')->table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoTV = $adjuntosTV->count();
                $this->linfo("Se encontraron {$counts->adjuntoTV} coincidencias en la base de datos");
                if($counts->adjuntoTV > 0) {
                    $this->linfo('Validando si existen los archivos');
                    foreach ($adjuntosTV as $adjuntoTV) {
                        $filePath = env('PATH_MEDIA_TELEVISION') . $adjuntoTV->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesTV++;
                                $this->linfo("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistTV++;
                            $counts->filesNotExist++;
                            $this->linfo("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesTV = 0;
                    $this->linfo('No hay archivos de televisión para borrar');
                }
            }

            if ($key == self::ID_RADIO) {
                $this->linfo('Buscando archivos de radio en la base de datos...');
                $adjuntosRD = DB::connection('mysql')->table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoRD = $adjuntosRD->count();
                $this->linfo("Se encontraron {$counts->adjuntoRD} coincidencias en la base de datos");
                if($counts->adjuntoRD > 0) {
                    $this->linfo('Validando si existen los archivos');
                    foreach ($adjuntosRD as $adjuntoRD) {
                        $filePath = env('PATH_MEDIA_RADIO') . $adjuntoRD->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesRD++;
                                $this->linfo("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistRD++;
                            $counts->filesNotExist++;
                            $this->linfo("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesRD = 0;
                    $this->linfo('No hay archivos de radio para borrar');
                }
            }

            if ($key == self::ID_PERIODICO) {
                $this->linfo('Buscando archivos de periodicos en la base de datos...');
                $adjuntosPE = DB::connection('mysql')->table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoPE = $adjuntosPE->count();
                $this->linfo("Se encontraron {$counts->adjuntoPE} coincidencias en la base de datos");
                if($counts->adjuntoPE > 0) {
                    $this->linfo('Validando si existen los archivos');
                    foreach ($adjuntosPE as $adjuntoPE) {
                        $filePath = env('PATH_MEDIA_PERIODICO') . $adjuntoPE->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesPE++;
                                $this->linfo("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistPE++;
                            $counts->filesNotExist++;
                            $this->linfo("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesPE = 0;
                    $this->linfo('No hay archivos de periodico para borrar');
                }
            }

            if ($key == self::ID_REVISTA) {
                $this->linfo('Buscando archivos de revista en la base de datos...');
                $adjuntosRE = DB::connection('mysql')->table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoRE = $adjuntosRE->count();
                $this->linfo("Se encontraron {$counts->adjuntoRE} coincidencias en la base de datos");
                if($counts->adjuntoRE > 0) {
                    $this->linfo('Validando si existen los archivos');
                    foreach ($adjuntosRE as $adjuntoRE) {
                        $filePath = env('PATH_MEDIA_REVISTA') . $adjuntoRE->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesRE++;
                                $this->linfo("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistRE++;
                            $counts->filesNotExist++;
                            $this->linfo("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesRE = 0;
                    $this->linfo('No hay archivos de revistas para borrar');
                }
            }

            if ($key == self::ID_INTERNET) {
                $this->linfo('Buscando archivos de internet en la base de datos...');
                $adjuntosIN = DB::connection('mysql')->table('adjunto')
                    ->select('nombre_archivo')
                    ->whereIn('id_noticia', $idNoticias)
                    ->get();
                $counts->adjuntoIN = $adjuntosIN->count();
                $this->linfo("Se encontraron {$counts->adjuntoIN} coincidencias en la base de datos");
                if($counts->adjuntoIN > 0) {
                    $this->linfo('Validando si existen los archivos');
                    foreach ($adjuntosIN as $adjuntoIN) {
                        $filePath = env('PATH_MEDIA_INTERNET') . $adjuntoIN->nombre_archivo;
                        if (file_exists($filePath)) {
                            if(unlink($filePath)) {
                                $counts->deletedFiles++;
                                $counts->deletedFilesIN++;
                                $this->linfo("Se ha borrado el archivo {$filePath}");
                            }
                        }else {
                            $counts->filesNotExistIN++;
                            $counts->filesNotExist++;
                            $this->linfo("El archivo {$filePath} no existe");
                        }
                    }
                } else {
                    $counts->deletedFilesIN = 0;
                    $this->linfo('No hay archivos de internet para borrar');
                }
            }
        }

        //eliminando cartones
        $this->linfo("Obteniendo los cartones entre {$fechaIni} y {$fechaFin}");
        $cartones = DB::connection('mysql')->table('carton')
            ->select('imagen')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->cartones = $cartones->count();
        $this->linfo("Numero de cartones: {$counts->cartones}");
        $this->linfo("Resultado: {$cartones}");
        $this->linfo('Iniciando el borrado de archivos de cartones.');
        $this->linfo('Validando si existen los archivos');
        if($counts->cartones > 0) {
            foreach ($cartones as $objCarton) {
                $filePath = env('PATH_MEDIA_CARTONES') . $objCarton->imagen;
                if (file_exists($filePath)) {
                    if(unlink($filePath)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesCartones++;
                        $this->linfo("Se ha borrado el archivo {$filePath}");
                    }
                }else {
                    $counts->filesNotExistCartones++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePath} no existe");
                }
            }
        } else {
            $counts->deletedFilesCartones = 0;
            $this->linfo('No hay archivos de cartones para borrar');
        }

        //eliminando columnas financieras
        $this->linfo("Obteniendo las columnas financieras entre {$fechaIni} y {$fechaFin}");
        $colFinancieras = DB::connection('mysql')->table('columna_financiera')
            ->select('imagen_jpg', 'archivo_pdf')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->columnasFinancieras = $colFinancieras->count();
        $this->linfo("Numero de columnas financieras: {$counts->columnasFinancieras}");
        $this->linfo("Resultado: {$colFinancieras}");
        $this->linfo('Iniciando el borrado de archivos de columnas financieras.');
        $this->linfo('Validando si existen los archivos');
        if($counts->columnasFinancieras > 0) {
            foreach ($colFinancieras as $colFinanciera) {
                $filePathImagen = env('PATH_MEDIA_COL_FINANCIERAS') . $colFinanciera->imagen_jpg;
                if (file_exists($filePathImagen)) {
                    if(unlink($filePathImagen)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColFin++;
                        $this->linfo("Se ha borrado el archivo {$filePathImagen}");
                    }
                }else {
                    $counts->filesNotExistColFin++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePathImagen} no existe");
                }
                $filePathDoc = env('PATH_MEDIA_COL_FINANCIERAS') . $colFinanciera->archivo_pdf;
                if (file_exists($filePathDoc)) {
                    if(unlink($filePathDoc)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColFin++;
                        $this->linfo("Se ha borrado el archivo {$filePathDoc}");
                    }
                }else {
                    $counts->filesNotExistColFin++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePathDoc} no existe");
                }
            }
        } else {
            $counts->columnasFinancieras = 0;
            $this->linfo('No hay archivos de columnas financieras para borrar');
        }

        //eliminando columnas politicas
        $this->linfo("Obteniendo las columnas politicas entre {$fechaIni} y {$fechaFin}");
        $colPoliticas = DB::connection('mysql')->table('columna_politica')
            ->select('imagen_jpg', 'archivo_pdf')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->columnasPoliticas = $colPoliticas->count();
        $this->linfo("Numero de columnas politicas: {$counts->columnasPoliticas}");
        $this->linfo("Resultado: {$colPoliticas}");
        $this->linfo('Iniciando el borrado de archivos de columnas politicas.');
        $this->linfo('Validando si existen los archivos');
        if($counts->columnasPoliticas > 0) {
            foreach ($colPoliticas as $colPolitica) {
                $filePathImagen = env('PATH_MEDIA_COL_POLITICAS') . $colPolitica->imagen_jpg;
                if (file_exists($filePathImagen)) {
                    if(unlink($filePathImagen)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColPol++;
                        $this->linfo("Se ha borrado el archivo {$filePathImagen}");
                    }
                }else {
                    $counts->filesNotExistColPol++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePathImagen} no existe");
                }
                $filePathDoc = env('PATH_MEDIA_COL_POLITICAS') . $colPolitica->archivo_pdf;
                if (file_exists($filePathDoc)) {
                    if(unlink($filePathDoc)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesColPol++;
                        $this->linfo("Se ha borrado el archivo {$filePathDoc}");
                    }
                }else {
                    $counts->filesNotExistColPol++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePathDoc} no existe");
                }
            }
        } else {
            $counts->columnasPoliticas = 0;
            $this->linfo('No hay archivos de columnas politicas para borrar');
        }

        //eliminando primeras planas
        $this->linfo("Obteniendo las primeras planas entre {$fechaIni} y {$fechaFin}");
        $primerasPlanas = DB::connection('mysql')->table('primera_plana')
            ->select('imagen')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();

        $counts->primerasPlanas = $primerasPlanas->count();
        $this->linfo("Numero de primeras planas: {$counts->primerasPlanas}");
        $this->linfo("Resultado: {$primerasPlanas}");
        $this->linfo('Iniciando el borrado de archivos de primeras planas.');
        $this->linfo('Validando si existen los archivos');
        if($counts->primerasPlanas > 0) {
            foreach ($primerasPlanas as $objPrimeraPlana) {
                $filePath = env('PATH_MEDIA_PRIMERAS_PLANAS') . $objPrimeraPlana->imagen;
                if (file_exists($filePath)) {
                    if(unlink($filePath)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesPrimeraPlana++;
                        $this->linfo("Se ha borrado el archivo {$filePath}");
                    }
                }else {
                    $counts->filesNotExistPrimeraPlana++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePath} no existe");
                }
            }
        } else {
            $counts->deletedFilesPrimerasPlanas = 0;
            $this->linfo('No hay archivos de primeras planas para borrar');
        }

        return response()->json(['reporte de noticias:' => $counts]);

    }

    protected function deleteNew ( $idNew, &$counts) {
        $this->linfo("Buscando archivos de {$type->descripcion} en la base de datos...");
        $adjuntos = Adjunto::Select('nombre_archivo')
            ->whereIn('id_noticia', $idNew)
            ->get();
        $counts->adjuntoTV = $adjuntosTV->count();
        $this->linfo("Se encontraron {$counts->adjuntoTV} coincidencias en la base de datos");
        if($counts->adjuntoTV > 0) {
            $this->linfo('Validando si existen los archivos');
            foreach ($adjuntosTV as $adjuntoTV) {
                $filePath = env('PATH_MEDIA_TELEVISION') . $adjuntoTV->nombre_archivo;
                if (file_exists($filePath)) {
                    if(unlink($filePath)) {
                        $counts->deletedFiles++;
                        $counts->deletedFilesTV++;
                        $this->linfo("Se ha borrado el archivo {$filePath}");
                    }
                }else {
                    $counts->filesNotExistTV++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePath} no existe");
                }
            }
        } else {
            $counts->deletedFilesTV = 0;
            $this->linfo('No hay archivos de televisión para borrar');
        }
    }
}

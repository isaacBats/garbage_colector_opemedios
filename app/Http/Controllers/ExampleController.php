<?php

namespace App\Http\Controllers;

use App\Adjunto;
use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\TipoFuenteController;
use DB;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    private $adjuntoController;
    
    private $noticiaController;

    private $tipoFuenteController;

    private $tiposFuente;

    public function __construct( TipoFuenteController $tipoFuenteController, NoticiaController $noticiaController, AdjuntoController $adjuntoController )
    {
        $this->noticiaController = $noticiaController;
        $this->tipoFuenteController = $tipoFuenteController;
        $this->adjuntoController = $adjuntoController;

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

        $this->linfo('Iniciando el borrado de archivos de noticias.');
        foreach ($groupBySource as $key => $idNoticias) {
            $this->noticiaController->deleteNewByType($key, $idNoticias, $counts, $this->tiposFuente);
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
}

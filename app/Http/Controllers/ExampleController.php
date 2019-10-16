<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\CartonController;
use App\Http\Controllers\ColumnaFinancieraController;
use App\Http\Controllers\ColumnaPoliticaController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\TipoFuenteController;
use DB;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    private $adjuntoController;
    
    private $cartonController;
    
    private $columnaFinancieraController;
    
    private $columnaPoliticaController;
    
    private $noticiaController;

    private $primeraPlanaController;

    private $tipoFuenteController;

    private $tiposFuente;

    public function __construct( TipoFuenteController $tipoFuenteController, 
        NoticiaController $noticiaController, 
        AdjuntoController $adjuntoController,
        CartonController $cartonController,
        ColumnaFinancieraController $columnaFinancieraController,
        ColumnaPoliticaController $columnaPoliticaController,
        PrimeraPlanaController $primeraPlanaController)
    {
        $this->adjuntoController = $adjuntoController;
        $this->cartonController = $cartonController;
        $this->columnaFinancieraController = $columnaFinancieraController;
        $this->columnaPoliticaController = $columnaPoliticaController;
        $this->noticiaController = $noticiaController;
        $this->tipoFuenteController = $tipoFuenteController;
        $this->primeraPlanaController = $primeraPlanaController;

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
        foreach ($groupBySource as $keyTipoFuente => $idNoticias) {
            $this->noticiaController->deleteNewByType($keyTipoFuente, $idNoticias, $counts, $this->tiposFuente);
        }

        //eliminando cartones
        $this->linfo("Obteniendo los cartones entre {$fechaIni} y {$fechaFin}");
        $cartones = $this->cartonController->getCartonesByData($fechaIni, $fechaFin);

        $counts->cartones = $cartones->count();
        $this->linfo("Numero de cartones: {$counts->cartones}");
        $this->linfo("Resultado: {$cartones}");
        $this->linfo('Iniciando el borrado de archivos de cartones.');
        $this->linfo('Validando si existen los archivos');
        $this->cartonController->deleteCartones($cartones, $counts);

        //eliminando columnas financieras
        $this->linfo("Obteniendo las columnas financieras entre {$fechaIni} y {$fechaFin}");
        $colFinancieras = $this->columnaFinancieraController->getColumnasFinancierasByData($fechaIni, $fechaFin);

        $counts->columnasFinancieras = $colFinancieras->count();
        $this->linfo("Numero de columnas financieras: {$counts->columnasFinancieras}");
        $this->linfo("Resultado: {$colFinancieras}");
        $this->linfo('Iniciando el borrado de archivos de columnas financieras.');
        $this->linfo('Validando si existen los archivos');
        $this->columnaFinancieraController->deleteColumnasFinancieras($colFinancieras, $counts);

        //eliminando columnas politicas
        $this->linfo("Obteniendo las columnas politicas entre {$fechaIni} y {$fechaFin}");
        $colPoliticas = $this->columnaPoliticaController->getColumnasPoliticasByData($fechaIni, $fechaFin);

        $counts->columnasPoliticas = $colPoliticas->count();
        $this->linfo("Numero de columnas politicas: {$counts->columnasPoliticas}");
        $this->linfo("Resultado: {$colPoliticas}");
        $this->linfo('Iniciando el borrado de archivos de columnas politicas.');
        $this->linfo('Validando si existen los archivos');
        $this->columnaPoliticaController->deleteColumnasFinancieras($colPoliticas, $counts);

        //eliminando primeras planas
        $this->linfo("Obteniendo las primeras planas entre {$fechaIni} y {$fechaFin}");
        $primerasPlanas = $this->primeraPlanaController->getPrimerasPlanasByData($fechaIni, $fechaFin);

        $counts->primerasPlanas = $primerasPlanas->count();
        $this->linfo("Numero de primeras planas: {$counts->primerasPlanas}");
        $this->linfo("Resultado: {$primerasPlanas}");
        $this->linfo('Iniciando el borrado de archivos de primeras planas.');
        $this->linfo('Validando si existen los archivos');
        $this->primeraPlanaController->deletePrimerasPlanas($primerasPlanas, $counts);

        return response()->json(['reporte de noticias:' => $counts]);

    }
}

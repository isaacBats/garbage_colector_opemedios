<?php

namespace App\Http\Controllers;

use App\PrimeraPlana;
use DB;
use Illuminate\Http\Request;

class PrimeraPlanaController extends Controller
{

    public function getPrimerasPlanasByData ($dateMin, $dateMax) {
        return PrimeraPlana::Select('imagen')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }

    public function deletePrimerasPlanas ($primerasPlanas, &$counts, &$backup) {
        if($counts->primerasPlanas > 0) {
            foreach ($primerasPlanas as $objPrimeraPlana) {
                $filePath = env('PATH_MEDIA_PRIMERAS_PLANAS') . $objPrimeraPlana->imagen;
                if (file_exists($filePath)) {
                    $backup->addFile($filePath, $objPrimeraPlana->imagen);
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
    }
}

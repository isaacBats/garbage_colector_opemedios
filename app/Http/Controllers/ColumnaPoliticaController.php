<?php

namespace App\Http\Controllers;

use App\ColumnaPolitica;
use DB;
use Illuminate\Http\Request;

class ColumnaPoliticaController extends Controller
{

    public function getColumnasPoliticasByData ($dateMin, $dateMax) {
        return ColumnaPolitica::Select('imagen_jpg', 'archivo_pdf')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }

    public function deleteColumnasFinancieras ($colPoliticas, &$counts) {
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
    }
}

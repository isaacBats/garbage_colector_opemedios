<?php

namespace App\Http\Controllers;

use App\ColumnaFinanciera;
use DB;
use Illuminate\Http\Request;

class ColumnaFinancieraController extends Controller
{

    public function getColumnasFinancierasByData ($dateMin, $dateMax) {
        return ColumnaFinanciera::Select('imagen_jpg', 'archivo_pdf')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }

    public function deleteColumnasFinancieras ($colFinancieras, &$counts, &$backup) {
        if($counts->columnasFinancieras > 0) {
            foreach ($colFinancieras as $colFinanciera) {
                $filePathImagen = env('PATH_MEDIA_COL_FINANCIERAS') . $colFinanciera->imagen_jpg;
                if (file_exists($filePathImagen)) {
                    $backup->addFile($filePathImagen, $colFinanciera->imagen_jpg);
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
                    $backup->addFile($filePathDoc, $colFinanciera->archivo_pdf);
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
    }
}

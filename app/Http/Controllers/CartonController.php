<?php

namespace App\Http\Controllers;

use App\Carton;
use DB;
use Illuminate\Http\Request;

class CartonController extends Controller
{

    public function getCartonesByData ($dateMin, $dateMax) {
        return Carton::Select('imagen')
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }

    public function deleteCartones ($cartones, &$counts, &$backup) {
        if($counts->cartones > 0) {
            foreach ($cartones as $objCarton) {
                $filePath = env('PATH_MEDIA_CARTONES') . $objCarton->imagen;
                if (file_exists($filePath)) {
                    $backup->addFile($filePath, $objCarton->imagen);
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
    }
}

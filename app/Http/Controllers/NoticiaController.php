<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AdjuntoController;
use App\Noticia;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NoticiaController extends Controller
{
    protected $adjuntoController;

    public function __construct(AdjuntoController $adjuntoController) {
        $this->adjuntoController = $adjuntoController;
    }

    public function getNewsin ($dateMin, $dateMax) {

        return Noticia::Select('id_noticia', 'id_tipo_fuente as fuente') 
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }

    public function getTVNews($dateMin, $dateMax) {

        return Noticia::Select('id_noticia', 'id_tipo_fuente as fuente')
            ->where('id_tipo_fuente', 1) 
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }

    public function findOrFail($id) {
        return Noticia::findOrFail($id);
    }

    public function deleteNewByType ($fontTypeId, $idNoticias, &$counts, $tiposFuente, &$backup) {
        
        $fontType = Arr::get($tiposFuente->toArray(), $fontTypeId - 1);
        $fontUppercase = strtoupper(Str::slug($fontType['descripcion']));
        $this->linfo("Buscando archivos de {$fontType['descripcion']} en la base de datos...");
        $adjuntos = $this->adjuntoController->getAdjuntos($idNoticias);
        $labelAdjunto = "adjunto{$fontType['sigla']}";
        $labelDeleteFile = "deletedFiles{$fontType['sigla']}";
        $labelFileNotExist = "filesNotExist{$fontType['sigla']}";
        $counts->$labelAdjunto = $adjuntos->count();
        $this->linfo("Se encontraron {$counts->$labelAdjunto} archivos de  {$fontType['descripcion']} en la base de datos");
        if($counts->$labelAdjunto > 0) {
            $labelPathMedia ="PATH_MEDIA_{$fontUppercase}";
            foreach ($adjuntos as $archivo) {
                $filePath = env($labelPathMedia) . $archivo->nombre_archivo;
                if (file_exists($filePath)) {
                    $backup->addFile($filePath, $archivo->nombre_archivo);
                    if(unlink($filePath)) {
                        $counts->deletedFiles++;
                        $counts->$labelDeleteFile++;
                        $this->linfo("Se ha borrado el archivo {$filePath}");
                    }
                }else {
                    $counts->$labelFileNotExist++;
                    $counts->filesNotExist++;
                    $this->linfo("El archivo {$filePath} no existe");
                }
            }
        } else {
            $counts->$labelAdjunto = 0;
            $this->linfo("No hay archivos de {$fontType['description']} para borrar");
        }
    }
}

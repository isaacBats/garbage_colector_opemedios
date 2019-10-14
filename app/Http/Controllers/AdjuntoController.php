<?php

namespace App\Http\Controllers;

use App\Adjunto;
use Illuminate\Http\Request;

class AdjuntoController extends Controller
{
    public function getAdjuntos($idNews) {
        
        return Adjunto::Select('nombre_archivo')
                    ->whereIn('id_noticia', $idNews)
                    ->get();
    }
}

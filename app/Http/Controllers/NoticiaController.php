<?php

namespace App\Http\Controllers;

use App\Noticia;
use DB;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function getNewsin ( $dateMin, $dateMax) {

        return Noticia::Select('id_noticia', 'id_tipo_fuente as fuente') 
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$dateMin, $dateMax])
            ->get();
    }
}

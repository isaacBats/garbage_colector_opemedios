<?php

namespace App\Http\Controllers;

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

    public function __construct()
    {
        //
    }

    public function deteleCartones(Request $req)
    {
        $fechaIni = $req->query('fecha_inicio');
        $fechaFin = $req->query('fecha_fin');

        Log::info("Obteniendo las noticias entre {$fechaIni} y {$fechaFin}");
        $noticias = DB::table('noticia')
            ->select('id_noticia', 'id_tipo_fuente as fuente') 
            ->whereBetween(DB::raw("date_format(fecha, '%Y-%m')"), [$fechaIni, $fechaFin])
            ->get();
        
        Log::info("Numero de noticias: {$noticias->count()}");
        Log::info("Resultado: {$noticias}");
        Log::info('Ordenando noticias por fuente...');
        $groupBySource = array();
        foreach ($noticias as $noticia) {
            $groupBySource[$noticia->fuente][] = $noticia->id_noticia;
        }

        Log::info('Iniciando el borrado de archivos de noticias.');
        foreach ($groupBySource as $key => $idNoticias) {
            echo public_path();
            exit;
            if ($key == ID_TELEVISION) {

            }
        }

        return response()->json($groupBySource, 200);
    }
}

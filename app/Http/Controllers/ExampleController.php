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
            if(in_array($noticia->fuente, $groupBySource)) {
                $groupBySource[$noticia->fuente][] = $noticia->id_noticia;
            }
            $groupBySource[$noticia->fuente][] = $noticia->id_noticia;
        }

        return $groupBySource;

        // $tipos = array_values( array_unique( array_column( $products_in_collection, 'tipo' ) ) );
        // $products = array();
        // foreach ($products_in_collection as $item)
        // {                
        //     if( in_array( $item['tipo'], $tipos ) )
        //     {
        //         $products[ $item['tipo'].'/'.$item['_type'] ][] = $item;
        //     }
        // }

        return response()->json($noticias, 200);
    }
}

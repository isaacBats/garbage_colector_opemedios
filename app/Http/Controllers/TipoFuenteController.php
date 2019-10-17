<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoFuente;

class TipoFuenteController extends Controller
{
    public function getAllFontTypes () {

        $fontTypes = TipoFuente::all()->toArray();
        foreach ($fontTypes as &$font) {
            if (strstr($font['descripcion'], 'Tele')) {
                $font['sigla'] = 'TV';
            } elseif (strstr($font['descripcion'], 'Radio')) {
                $font['sigla'] = 'RD';
            } elseif (strstr($font['descripcion'], 'Peri')) {
                $font['sigla'] = 'PE';
            } elseif (strstr($font['descripcion'], 'Revi')) {
                $font['sigla'] = 'RE';
            } elseif (strstr($font['descripcion'], 'Inte')) {
                $font['sigla'] = 'IN';
            } else {
                $font['sigla'] = '';
            }
        }
        
        return collect($fontTypes);

    }
}

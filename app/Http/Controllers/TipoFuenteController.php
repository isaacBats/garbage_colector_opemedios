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
                $font['media'] = 'TELEVISION';
            } elseif (strstr($font['descripcion'], 'Radio')) {
                $font['sigla'] = 'RD';
                $font['media'] = 'RADIO';
            } elseif (strstr($font['descripcion'], 'Peri')) {
                $font['sigla'] = 'PE';
                $font['media'] = 'PERIODICO';
            } elseif (strstr($font['descripcion'], 'Revi')) {
                $font['sigla'] = 'RE';
                $font['media'] = 'REVISTA';
            } elseif (strstr($font['descripcion'], 'Inte')) {
                $font['sigla'] = 'IN';
                $font['media'] = 'INTERNET';
            } else {
                $font['sigla'] = '';
            }
        }
        
        return collect($fontTypes);

    }
}

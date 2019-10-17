<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Log;

class Controller extends BaseController
{
    protected function linfo( $message ) {

        Log::info($message);
        
    }
}

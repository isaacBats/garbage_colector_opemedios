<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Log;
// use Symfony\Component\Process\Exception\ProcessFailedException;
// use Symfony\Component\Process\Process;

class SystemController extends Controller
{

    const SYMBOLS = array('bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');

    public function index () {

        // sprintf('%.2f '. self::SYMBOLS[$exp], ($ds/pow(1024, floor($exp))));

        return ['total' => $this->get_total_space_disk(), 'free' => $this->get_free_space_disk(), 'In use' => $this->get_used_space_disk(), ]; 

        // $process = new Process(['df', '-h']);
        // $process->run();

        
        
    }

    protected function get_total_space_disk () {

        $bytes = disk_total_space(env('PATH_DISK_MEDIA'));
        
 
        return $this->convert($bytes);

    }

    protected function get_free_space_disk () {

        $bytes = disk_free_space(env('PATH_DISK_MEDIA'));

        return $this->convert($bytes);

    }

    protected function get_used_space_disk () {

        $total = $this->get_total_space_disk()['val'];
        $free = $this->get_free_space_disk()['val'];
        $used = $total - $free;
        $exp = $this->get_exponential($used);
        
        return  ['val' => $used, 'exp' => $exp];

    }

    private function get_exponential( $bytes ) {

        return $bytes ? floor(log($bytes) / log(1024)) : 0;
    }

    protected function convert ( $bytes ) {

        $exp = $this->get_exponential($bytes);

        return ['val' => $bytes/pow(1024, floor($exp)), 'exp' => $exp];
    }
}
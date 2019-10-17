<?php

namespace App\Http\Controllers;

class SystemController extends Controller
{

    private $symbol = array('bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');

    public function index () {

        return [
            'Total' => $this->convert($this->get_total_space_disk()), 
            'In_use' => $this->convert($this->get_used_space_disk()), 
            'Free' => $this->convert($this->get_free_space_disk()), ];
    }

    private function get_total_space_disk () {

        return disk_total_space(env('PATH_DISK_MEDIA'));
    }

    private function get_free_space_disk () {

        return disk_free_space(env('PATH_DISK_MEDIA'));
    }

    private function get_used_space_disk () {

        return $this->get_total_space_disk() - $this->get_free_space_disk();
    }

    private function convert ( $bytes ) {

        $exp = $bytes ? floor(log($bytes) / log(1024)) : 0;
        $value = round($bytes/pow(1024, floor($exp)), 2);
        return "{$value} {$this->symbol[$exp]}";
    }
}
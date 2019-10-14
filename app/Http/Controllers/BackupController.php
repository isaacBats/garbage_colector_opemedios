<?php

namespace App\Http\Controllers;

use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        // test update image

        $my_file = 'file.txt';
        // $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
        // $data = 'Test data to see if this works!';
        // fwrite($handle, $data);
        // fclose($handle);
        $storagePath = Storage::disk('s3')->put($my_file, 'Test data to see if this works!. Data File system');

        $newLast = News::find(599084);
        return $newLast;
    }
}

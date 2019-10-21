<?php

namespace App\Http\Controllers;

use App\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;

class BackupController extends Controller
{
    public static function moveToS3($fileName, $backup) {

        try {
            $folderS3 = 'backups';
            $filePath = "/{$folderS3}/{$fileName}";
            
            return Storage::disk('s3')->put($filePath, fopen($backup, 'r+'));
        
        } catch (S3Exception $e) {

            $this->lerror("Error S3: {$e->getMessage()}");
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        // test update image

        $my_file = 'test.txt';
        $storagePath = Storage::disk('s3')->put($my_file, 'Test data to see if this works!. Data File system');


        /**
         * Para lograr subir un archivo a dreamhost instale y configure las librerias de
         * league/flysystem-aws-s3-v3
         * aws/aws-sdk-php-laravel
         * 
         * configurando el archivo filesystem.php en el apartado de s3 de la siguiente manera
         * 's3' => [
         *  'driver' => 's3',
         *   'key' => env('AWS_KEY'),
         *   'secret' => env('AWS_SECRET'),
         *   'region' => env('AWS_REGION'),
         *   'version' => '2006-03-01',
         *   'endpoint' => env('AWS_ENDPOINT'),
         *   'bucket_name' => env('AWS_BUCKET'),
         * ],
         * agregre estos service provider 
         * $app->register(App\Providers\AwsS3ServiceProvider::class);
         * $app->register(Aws\Laravel\AwsServiceProvider::class);
         * 
         * y las variables de entorno quedaron de la siguiente forma
         * 
         * AWS_KEY=DHA9GSFFQYRP5EPYL9UL
         * AWS_SECRET=kyoQlrYPE10OvlqbOSLF1gNImZESvkT1AOWoMybv
         * AWS_REGION=us-east-1
         * AWS_BUCKET=opemedios
         * AWS_ENDPOINT=https://objects-us-east-1.dream.io
         */

        $newLast = Noticia::find(599084);
        return $newLast;
    }
}

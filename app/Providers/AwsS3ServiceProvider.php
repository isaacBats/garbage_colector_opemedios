<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Aws\Laravel\AwsServiceProvider;
use Illuminate\Support\Facades\Storage;

class AwsS3ServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('s3', function($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => $config['version'],
                'endpoint' => $config['endpoint'],
                'ua_append' => [
                    'L5MOD/' . AwsServiceProvider::VERSION,
                ],
            ]);

            return new Filesystem(new AwsS3Adapter($client, $config['bucket_name']));
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
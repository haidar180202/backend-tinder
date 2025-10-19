<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('gcs', function ($app, $config) {
            $storageClient = new StorageClient([
                'projectId' => $config['project_id'],
            ]);
            $bucket = $storageClient->bucket($config['bucket']);
            $adapter = new GoogleCloudStorageAdapter($bucket, $config['path_prefix'] ?? '');
            $flysystem = new Filesystem($adapter, $config);

            return new FilesystemAdapter($flysystem, $adapter, $config);
        });
       
    }
}
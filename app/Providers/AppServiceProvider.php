<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

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
        Storage::extend('azure-blob', function ($app, $config) {
            $connectionString = $config['connection_string'];
            $container = $config['container'];
            
            $client = BlobRestProxy::createBlobService($connectionString);
            $adapter = new AzureBlobStorageAdapter($client, $container);
            $filesystem = new Filesystem($adapter);
            
            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}

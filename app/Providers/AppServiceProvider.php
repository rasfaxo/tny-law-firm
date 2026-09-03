<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;

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
        if (config('observability.enabled', false)) {
            DB::listen(function (QueryExecuted $query): void {
                if ($query->time < config('observability.slow_query_ms', 250)) {
                    return;
                }

                // Query bindings are deliberately not logged because they can
                // contain credentials, personal data, or case details.
                $fingerprint = preg_replace('/\s+/', ' ', trim($query->sql));

                Log::channel(config('observability.channel', 'stack'))->warning('performance.slow_query', [
                    'duration_ms' => round($query->time, 2),
                    'connection' => $query->connectionName,
                    'fingerprint' => $fingerprint,
                ]);
            });
        }

        Storage::extend('azure', function ($app, $config) {
            $connectionString = ! empty($config['connection_string'])
                ? $config['connection_string']
                : sprintf(
                    'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s;',
                    $config['name'] ?? $config['account_name'] ?? '',
                    $config['key'] ?? $config['account_key'] ?? ''
                );

            $client = BlobRestProxy::createBlobService($connectionString);

            $settings = null;
            try {
                $settings = StorageServiceSettings::createFromConnectionString($connectionString);
            } catch (\Throwable) {
                // If connection string parsing fails, fallback gracefully
            }

            $adapter = new AzureBlobStorageAdapter(
                $client,
                $config['container'] ?? '',
                $config['prefix'] ?? '',
                null,
                $config['max_results_for_contents_listing'] ?? 5000,
                $config['visibility_handling'] ?? AzureBlobStorageAdapter::ON_VISIBILITY_THROW_ERROR,
                $settings
            );

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if (
            ! $this->app->environment('local', 'testing')
            && str_starts_with((string) config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }

        Queue::failing(function (JobFailed $event): void {
            Log::error('queue.job_failed', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'exception' => $event->exception::class,
            ]);
        });

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
    }
}

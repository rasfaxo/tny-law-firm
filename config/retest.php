<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Temporary Azure v1.0.0 retest fixtures
    |--------------------------------------------------------------------------
    |
    | These values are used only by RetestFixtureSeeder. The seeder also
    | enforces the exact staging host, container, and prefix before writing.
    | Keep the feature disabled outside the one-time fixture deployment.
    |
    */
    'fixtures' => [
        'enabled' => (bool) env('RETEST_FIXTURES_ENABLED', false),
        'confirmation' => env('RETEST_FIXTURE_CONFIRMATION'),
        'password' => env('RETEST_FIXTURE_PASSWORD'),
    ],
];

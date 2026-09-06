<?php

return [
    'ready' => (bool) env('PRIVACY_POLICY_READY', false),
    'policy_version' => env('PRIVACY_POLICY_VERSION'),
    'effective_date' => env('PRIVACY_POLICY_EFFECTIVE_DATE'),
];

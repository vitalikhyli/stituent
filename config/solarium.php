<?php

// https://petericebear.github.io/laravel-php-solarium-integration-20160725/

return [
    'endpoint' => [
        'solr-server' => [
            // 'host' => env('SOLR_HOST', '142.93.245.57'),
            'host' => env('SOLR_HOST', 'solr.fluency.software'),
            'port' => env('SOLR_PORT', '8983'),
            'path' => env('SOLR_PATH', '/'),
            // 'core' => env('SOLR_CORE', 'ny_TEST')
            'core' => env('SOLR_CORE', 'ny'),
            // 'core' => env('SOLR_CORE', 'ny_test'),
        ],
    ],
];

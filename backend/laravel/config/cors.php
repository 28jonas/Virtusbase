<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173', 'http://localhost:3000', 'http://localhost:8100', 'http://localhost:3001', 'http://192.168.0.126:5173','http://192.168.0.126:8080','http://localhost:8100'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
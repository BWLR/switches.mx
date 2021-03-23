<?php

return [

    'enabled' => env('STATAMIC_OAUTH_ENABLED', false),

    'providers' => [
        'reddit',
    ],

    'routes' => [
        'login' => 'oauth/{provider}',
        'callback' => 'oauth/{provider}/callback',
    ],

];

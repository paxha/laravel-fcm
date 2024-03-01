<?php

return [
    'default' => 'fcm',

    'channels' => [
        'fcm' => [
            'project' => env('GOOGLE_PROJECT'),
            'service_account' => env('GOOGLE_SERVICE_ACCOUNT'),
        ],

//        'fcm.project-1' => [
//            'project' => env('PROJECT_1_GOOGLE_PROJECT'),
//            'service_account' => env('PROJECT_1_SERVICE_ACCOUNT'),
//        ],
    ],
];

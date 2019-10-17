<?php
$config =  [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'temporary' => [
            'driver' => 'local',
            'root' => storage_path('app/public/temporary'),
            'url' => env('APP_URL') . '/storage/temporary',
            'visibility' => 'public',
        ],

        'resident_credentials' => [
            'driver' => 'local',
            'root' => storage_path('app/private/residents/credentials'),
            'visibility' => 'public',
        ],

        'request_downloads' => [
            'driver' => 'local',
            'root' => storage_path('app/private/request/downloads'),
            'visibility' => 'public',
        ],


        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

    ],

    //https://www.freeformatter.com/mime-types-list.html
    //https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
    'mime_types' => [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
//        "docx" => "application/octet-stream",
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',

    ]
];

$mediaCategories = ConstFileCategories::MediaCategories;
$disks = $config['disks'];
$categoryResources = [
    'buildings',
    'units',
    'quarters',
];

foreach ($mediaCategories as $category) {
    foreach ($categoryResources as $resource) {
        $disks[$resource . '_' . $category] = [
            'driver' => 'local',
            'root' => storage_path('app/public/' . $resource . '/' . $category),
            'url' => env('APP_URL').'/storage/' . $resource . '/' . $category,
            'visibility' => 'public',
        ];
    }
}

$simpleResources = [
    'pinboard',
    'listings',
    'residents',
    'contracts',
    'requests'
];

foreach ($simpleResources as $resource) {
    $disks[$resource . '_media'] = [
        'driver' => 'local',
        'root' => storage_path('app/public/' . $resource . '/media'),
        'url' => env('APP_URL').'/storage/' . $resource . '/media',
        'visibility' => 'public',
    ];
}

$config['disks'] = $disks;



return $config;

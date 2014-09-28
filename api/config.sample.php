<?php
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => 'fruits',
        'user'      => 'fruits',
        'password'  => '',
        'charset'   => 'utf8',
    ),
));

// Proxy
$aContext = array(
    'http' => array(
        'proxy' => 'tcp://129.104.247.2:8080',
        'request_fulluri' => true,
    ),
    'https' => array(
        'proxy' => 'tcp://129.104.247.2:8080',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);

$tmdbKey = '10693a5e1e693837a6c36153f260d8d3';
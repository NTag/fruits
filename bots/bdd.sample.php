<?php
define('SMSDSN',      'mysql:dbname=fruits;host=localhost;charset=UTF8');
define('SMSUSERNAME', 'fruits');
define('SMSPASSWORD', '');

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

$tmdbKey = '10693a5e1e693837a6c36153f260d8d3'; // je laisse ma clé TMDB je suis gentil (enfin je l'utilise que pour fruits)

$useless = array(
    'folder.jpg',
    'desktop.ini',
    'thumbs.db',
    'albumartsmall.jpg',
    );

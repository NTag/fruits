<?php
require('bdd.php');

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

$useless = array(
    'folder.jpg',
    'desktop.ini',
    'thumbs.db',
    'albumartsmall.jpg',
    );
$uselessExt = array(
	'nfo',
	'jpg',
	'jpeg',
	'png',
	'tbn',
)

function slug($str) {
    $before = array(
        utf8_decode('ÀÁÂÃÄÅÒÓÔÕÖØÈÉÊËÐÇÌÍÎÏÙÚÛÜÑŠŽàáâãäåòóôõöøèéêëðçìíîïùúûüñšž'),
    );
 
    $after = array(
        utf8_decode('AAAAAAOOOOOOEEEEECIIIIUUUUNSZaaaaaaooooooeeeeeciiiiuuuunsz'),
    );

    $str = strtr(utf8_decode($str), $before[0], $after[0]);
 
    return utf8_encode($str);
}
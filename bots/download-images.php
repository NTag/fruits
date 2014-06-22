<?php
require('config.php');

echo "# Fruits v5
## Telechargement des images des series
## " . date('Y-m-d H:i:s') . "\n";

$list = json_decode(file_get_contents('http://fruits/api/series'));

foreach ($list as $l) {
    echo $l->nom;
    $infos = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/' . $l->tmdbid . '?api_key=' . $tmdbKey . '&language=fr', false, $cxContext));
    copy('https://image.tmdb.org/t/p/original/' . $infos->poster_path, '../api/data/series/poster/' . $l->tmdbid . '.jpg', $cxContext);
    copy('https://image.tmdb.org/t/p/w300/' . $infos->poster_path, '../api/data/series/poster/' . $l->tmdbid . '_w300.jpg', $cxContext);
    /*
    foreach ($infos->seasons as $s) {
        if (!empty($s->poster_path)) {
            copy('https://image.tmdb.org/t/p/original/' . $s->poster_path, '../api/data/series/poster/' . $l->tmdbid . '_s' . $s->season_number . '.jpg', $cxContext);
            echo '.';
        }
    }
    */
    echo "\n";
}


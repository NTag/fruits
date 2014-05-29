<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

$reqAddFilm = $bdd->prepare("INSERT INTO films
VALUES(:tmdbid, :title, :titlefr, :titleen, :overview, :genres, :budget, :popularity, :vote, :production, :release_date, :runtime)");

$fields = array('tmdbid', 'title', 'titlefr', 'titleen', 'overview', 'genres', 'budget', 'popularity', 'vote', 'production', 'release_date', 'runtime');

$reqAllId = $bdd->prepare("SELECT DISTINCT tmdbid
	FROM filmsf
	WHERE tmdbid NOT IN (SELECT tmdbid FROM films)");
$reqAllId->execute();
$films = $reqAllId->fetchAll();
$reqAllId->closeCursor();

$total = count($films);
$cinqp = ceil($total / 20);
$last = (-1) * $cinqp * 3;

echo $total . " films à identifier\n\n";
$i = 0;

foreach ($films as $s) {
	$pourc = ceil($i / $total * 100);
	if ($pourc % 10 == 0 and ($i - $last) > $cinqp) {
		$last = $i;
		echo "\n" . $pourc . '%  ';
	}
	$i++;
	
	$in['tmdbid'] = $s['tmdbid'];
	$infos = json_decode(file_get_contents('https://api.themoviedb.org/3/movie/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=en', false, $cxContext));
	$in['title'] = $infos->original_title;
	$in['titleen'] = $infos->title;
	$in['overview'] = $infos->overview;
	$in['genres'] = '';
	foreach ($infos->genres as $g) {
		$in['genres'] .= $g->name . ', ';
	}
	$in['budget'] = $infos->budget;
	$in['popularity'] = $infos->popularity;
	$in['vote'] = $infos->vote_average;
	$in['production'] = '';
	foreach ($infos->production_companies as $g) {
		$in['production'] .= $g->name . ', ';
	}
	$in['release_date'] = $infos->release_date;
	$in['runtime'] = $infos->runtime;
	$poster = $infos->poster_path;
	
	$infos = json_decode(file_get_contents('https://api.themoviedb.org/3/movie/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=fr', false, $cxContext));
	if (!empty($infos->overview)) {
		$in['overview'] = $infos->overview;
	}
	$in['titlefr'] = $infos->title;
	if (!empty($infos->poster_path)) {
		$poster = $infos->poster_path;
	}
	if (isset($infos->genres) and count($infos->genres) > 0) {
		$in['genres'] = '';
		foreach ($infos->genres as $g) {
			$in['genres'] .= $g->name . ', ';
		}
	}
	
	copy('https://image.tmdb.org/t/p/original/' . $poster, '../api/data/films/poster/' . $s['tmdbid'] . '.jpg', $cxContext);
    copy('https://image.tmdb.org/t/p/w300/' . $poster, '../api/data/films/poster/' . $s['tmdbid'] . '_w300.jpg', $cxContext);
	
	foreach ($fields as $f) {
		$reqAddFilm->bindValue(':' . $f, $in[$f]);
	}
	
	$reqAddFilm->execute();
	$reqAddFilm->closeCursor();
	
	echo ".";
	//exit();
}
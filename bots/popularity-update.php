<?php
require('config.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

echo "\n# Fruits v5
## Mise à jour de la popularité des films et séries
## " . date('Y-m-d H:i:s') . "\n";

// SQL
$reqUpFilm = $bdd->prepare("UPDATE films
	SET popularity = :popularity
	WHERE tmdbid = :tmdbid");
$reqUpSerie = $bdd->prepare("UPDATE series
	SET tpopularity = :popularity
	WHERE tmdbid = :tmdbid");

$reqAllFilms = $bdd->prepare("SELECT tmdbid
	FROM films");
$reqAllFilms->execute();
$films = $reqAllFilms->fetchAll();
$reqAllFilms->closeCursor();

$reqAllSeries = $bdd->prepare("SELECT tmdbid
	FROM series");
$reqAllSeries->execute();
$series = $reqAllSeries->fetchAll();
$reqAllSeries->closeCursor();

$total = count($films) + count($series);
$cinqp = ceil($total / 20);
$last = (-1) * $cinqp * 3;

echo count($films) . " films à identifier\n";
echo count($series) . " séries à identifier\n\n";
$i = 0;
echo "## Séries \n";
foreach ($series as $s) {
	$pourc = ceil($i / $total * 100);
	if ($pourc % 10 == 0 and ($i - $last) > $cinqp) {
		$last = $i;
		echo "\n" . $pourc . '%  ';
	}
	$i++;

	$infos = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=fr', false, $cxContext));
	$reqUpSerie->bindValue(':popularity', $infos->popularity);
	$reqUpSerie->bindValue(':tmdbid', $s['tmdbid']);
	$reqUpSerie->execute();
	$reqUpSerie->closeCursor();
	echo '.';
}

echo "\n## Films \n";
foreach ($films as $s) {
	$pourc = ceil($i / $total * 100);
	if ($pourc % 10 == 0 and ($i - $last) > $cinqp) {
		$last = $i;
		echo "\n" . $pourc . '%  ';
	}
	$i++;

	$infos = json_decode(file_get_contents('https://api.themoviedb.org/3/movie/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=fr', false, $cxContext));
	$reqUpFilm->bindValue(':popularity', $infos->popularity);
	$reqUpFilm->bindValue(':tmdbid', $s['tmdbid']);
	$reqUpFilm->execute();
	$reqUpFilm->closeCursor();

	echo '.';
}

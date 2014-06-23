<?php
require('config.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

echo "# Fruits v5
## Informations des episodes des series
## " . date('Y-m-d H:i:s') . "\n";

$reqUpdateEpisode = $bdd->prepare("UPDATE series_episodes
	SET tdate=:tdate, tname=:tname
	WHERE saison=:saison AND episode=:episode");

$reqAllSaisons = $bdd->prepare("SELECT series_saisons.id, series.tmdbid, series_saisons.numero
	FROM series_episodes
	LEFT JOIN series_saisons
	ON series_saisons.id = series_episodes.saison
	LEFT JOIN series On series.tmdbid = series_saisons.serie
	WHERE series_episodes.tname = '' AND series_episodes.tdate='2013-01-01' AND series.tmdbid IS NOT NULL
	GROUP BY series_saisons.id
	ORDER BY series.tmdbid ASC");
$reqAllSaisons->execute();
$episodes = $reqAllSaisons->fetchAll();
$reqAllSaisons->closeCursor();

foreach ($episodes as $s) {
	$infos = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/' . $s['tmdbid'] . '/season/' . $s['numero'] . '?api_key=' . $tmdbKey . '&language=en', false, $cxContext));
	
	foreach ($infos->episodes as $e) {
		echo 'e';
		$reqUpdateEpisode->bindValue(':tdate', $e->air_date);
		$reqUpdateEpisode->bindValue(':tname', $e->name);
		$reqUpdateEpisode->bindValue(':saison', $s['id']);
		$reqUpdateEpisode->bindValue(':episode', $e->episode_number);
		$reqUpdateEpisode->execute();
		$reqUpdateEpisode->closeCursor();	
	}

	//exit();
}
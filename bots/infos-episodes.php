<?php
require('config.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

echo "# Fruits v5
## Informations des episodes des series
## " . date('Y-m-d H:i:s') . "\n";

$reqUpdateEpisode = $bdd->prepare("UPDATE series_episodes
	SET tdate=:tdate, tname=:tname
	WHERE saison=:saison AND episode=:episode");

$reqAllSaisons = $bdd->prepare("SELECT series_saisons.id, numero, tmdbid
	FROM series_saisons
	LEFT JOIN series
	ON series.tmdbid = series_saisons.serie");
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
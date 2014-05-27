<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

$reqUpdateSerie = $bdd->prepare("UPDATE series
	SET tnbseasons=:tnbseasons, tpopularity=:tpopularity, tfirstdate=:tfirstdate, tlastdate=:tlastdate, tepisode_run_time=:tepisode_run_time, tgenres=:tgenres, tin_production=:tin_production,tnetwork=:tnetwork,torigin_country=:torigin_country,toverview=:toverview
	WHERE id=:id");

$reqAllSeries = $bdd->prepare("SELECT id, nom, tmdbid
	FROM series");
$reqAllSeries->execute();
$series = $reqAllSeries->fetchAll();
$reqAllSeries->closeCursor();

foreach ($series as $s) {
	echo $s['nom'];
	$infos = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=en', false, $cxContext));
	
	$reqUpdateSerie->bindValue(':tnbseasons', $infos->number_of_seasons);
	$reqUpdateSerie->bindValue(':tpopularity', $infos->popularity);
	$reqUpdateSerie->bindValue(':tfirstdate', $infos->first_air_date);
	$reqUpdateSerie->bindValue(':tlastdate', $infos->last_air_date);
	$reqUpdateSerie->bindValue(':tepisode_run_time', $infos->episode_run_time[0]);
	$genres = '';
	foreach ($infos->genres as $g) {
		$genres .= $g->name . ', ';
	}
	$reqUpdateSerie->bindValue(':tgenres', $genres);
	$reqUpdateSerie->bindValue(':tin_production', $infos->in_production);
	$reqUpdateSerie->bindValue(':tnetwork', $infos->networks[0]->name);
	$reqUpdateSerie->bindValue(':torigin_country', $infos->origin_country[0]);
	$reqUpdateSerie->bindValue(':toverview', $infos->overview);
	$reqUpdateSerie->bindValue(':id', $s['id']);
	$reqUpdateSerie->execute();
	$reqUpdateSerie->closeCursor();
	
	echo "\n";
	//exit();
}
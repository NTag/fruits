<?php
require('config.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

echo "# Fruits v5
## Informations des series
## " . date('Y-m-d H:i:s') . "\n";

$reqUpdateSerie = $bdd->prepare("UPDATE series
	SET tnbseasons=:tnbseasons, tpopularity=:tpopularity, tfirstdate=:tfirstdate, tlastdate=:tlastdate, tepisode_run_time=:tepisode_run_time, tgenres=:tgenres, tin_production=:tin_production,tnetwork=:tnetwork,torigin_country=:torigin_country,toverview=:toverview
	WHERE id=:id");

$reqAllSeries = $bdd->prepare("SELECT id, nom, tmdbid
	FROM series
	WHERE tnbseasons=0");
$reqAllSeries->execute();
$series = $reqAllSeries->fetchAll();
$reqAllSeries->closeCursor();

foreach ($series as $s) {
	echo $s['nom'];
	$infos = array();
	
	$infosa = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=en', false, $cxContext));
    $infos['tnbseasons'] = $infosa->number_of_seasons;
	$infos['tfirstdate'] = $infosa->first_air_date;
	$infos['tlastdate'] = $infosa->last_air_date;
	$infos['tepisode_run_time'] = $infosa->episode_run_time[0];
	$infos['tin_production'] = $infosa->in_production;
	$infos['tnetwork'] = $infosa->networks[0]->name;
	$infos['torigin_country'] = $infosa->origin_country[0];
	$infos['id'] = $s['id'];
	$infos['toverview'] = $infosa->overview;
	
	$infosa = json_decode(file_get_contents('https://api.themoviedb.org/3/tv/' . $s['tmdbid'] . '?api_key=' . $tmdbKey . '&language=fr', false, $cxContext));
	$infos['tpopularity'] = $infosa->popularity;
	if (!empty($infosa->overview)) {
    	$infos['toverview'] = $infosa->overview;
	}
	$genres = '';
	foreach ($infosa->genres as $g) {
		$genres .= $g->name . ', ';
	}
	$infos['tgenres'] = $genres;
	
	foreach ($infos as $k => $v) {
    	$reqUpdateSerie->bindValue(':' . $k, $v);
	}
	$reqUpdateSerie->execute();
	$reqUpdateSerie->closeCursor();
	
	echo "\n";
	//exit();
}
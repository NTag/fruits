<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

// SQL
$reqUpEpisode = $bdd->prepare("UPDATE series_episodes
	SET episode=:nep
	WHERE fichier=:id");

$reqAllFiles = $bdd->prepare("SELECT fichier, nom
	FROM series_episodes
	LEFT JOIN fichiers
	ON fichiers.id = series_episodes.fichier");
$reqAllFiles->execute();
$files = $reqAllFiles->fetchAll();
$reqAllFiles->closeCursor();

/*
$reqAllSeries = $bdd->prepare("SELECT series.id, sa.id AS saison, sa.numero
	FROM series
	LEFT JOIN series_saisons AS sa
	ON sa.serie = series.id");
$reqAllSeries->execute();
$seriesAndSa = $reqAllSeries->fetchAll();
$reqAllSeries->closeCursor();
*/


$total = count($files);
$cinqp = ceil($total / 20);
$last = (-1) * $cinqp * 3;

$series = array();
$not = array();

echo $total . " fichiers à identifier\n\n";
$i = 0;
foreach ($files as $f) {
	$pourc = ceil($i / $total * 100);
	if ($pourc % 10 == 0 and ($i - $last) > $cinqp) {
		$last = $i;
		echo "\n" . $pourc . '%  ';
	}
	$i++;
	$f['nom'] = str_replace('720p', '', $f['nom']);
	$f['nom'] = str_replace('480p', '', $f['nom']);
	$f['nom'] = str_replace('1080p', '', $f['nom']);
	$regex = '#^\W?(([0-9]{2,3}|epz)\W{1,3})?(\w.+)(\W|_){1,3}((s?[0-9]{1,2}x[0-9]{2})|(S[0-9]{1,2}E[0-9]{2})|(Saison [0-9]{1,2} Episode [0-9]{2})|(Season [0-9]{1,2} Episode [0-9]{2})).+$#is';
	$nom = preg_replace($regex, '$4', $f['nom']);
	if ($nom == $f['nom']) {
		echo 'P';
		continue;
	}
	$saison = preg_replace($regex, '$1', $f['nom']);
	if (empty($saison) or strtolower($saison) == 'epz' or strlen($saison) <= 2) {
		$saison = preg_replace($regex, '$5', $f['nom']);
		if ($saison == $f['nom']) {
			$saison = preg_replace('#^\W?(([0-9]{2,3}|epz)\W{1,3})?(\w.+)(\W|_){1,3}((s?[0-9]{1,2}x[0-9]{2})|(S[0-9]{1,2}E[0-9]{2})|(Saison [0-9]{1,2} Episode [0-9]{2})|(Season [0-9]{1,2} Episode [0-9]{2})|([0-9]{2,4})).+$#is', '$5', $f['nom']);
		}
	}
	$saison = strtolower($saison);
	if (preg_match('#^(s[0-9]{1,2}e[0-9]{2})$#isU', $saison)) {
		$nsaison = preg_replace('#^s([0-9]{1,2})e[0-9]{2}$#isU', '$1', $saison);
		$nep = preg_replace('#^s[0-9]{1,2}e([0-9]{2})$#isU', '$1', $saison);
	} elseif (preg_match('#^s?([0-9]{1,2})x([0-9]{2})$#isU', $saison)) {
		$nsaison = preg_replace('#^s?([0-9]{1,2})x([0-9]{2})$#isU', '$1', $saison);
		$nep = preg_replace('#^s?([0-9]{1,2})x([0-9]{2})$#isU', '$2', $saison);
	} elseif (preg_match('#^saison ([0-9]{1,2}) episode ([0-9]{2})$#isU', $saison)) {
		$nsaison = preg_replace('#^saison ([0-9]{1,2}) episode ([0-9]{2})$#isU', '$1', $saison);
		$nep = preg_replace('#^saison ([0-9]{1,2}) episode ([0-9]{2})$#isU', '$2', $saison);
	} elseif (preg_match('#^season ([0-9]{1,2}) episode ([0-9]{2})$#isU', $saison)) {
		$nsaison = preg_replace('#^season ([0-9]{1,2}) episode ([0-9]{2})$#isU', '$1', $saison);
		$nep = preg_replace('#^season ([0-9]{1,2}) episode ([0-9]{2})$#isU', '$2', $saison);
	} elseif (preg_match('#^[0-9]+$#', $saison)) {
		$nsaison = substr($saison, 0, strlen($saison) - 2);
		$nep = substr($saison, strlen($saison) - 2, 2);
	} else {
		continue;
	}
	
	$nsaison = intval($nsaison);
	$nep = intval($nep);
	
	// On ajoute l'épisode
	$reqUpEpisode->bindValue(':id', $f['fichier']);
	$reqUpEpisode->bindValue(':nep', $nep);
	$reqUpEpisode->execute();
	$reqUpEpisode->closeCursor();
	echo 'e';
	
	//echo $nom . " -- " . $f['chemin_complet'] . "\n";
}
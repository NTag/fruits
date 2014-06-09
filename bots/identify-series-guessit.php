<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

// SQL
$reqAddSerie = $bdd->prepare("INSERT INTO series
	VALUES('', :nom, :tmdbid,:tnbseasons, :tpopularity, :tfirstdate, :tlastdate, :tepisode_run_time, :tgenres, :tin_production,:tnetwork,:torigin_country,:toverview)");
$reqAddSaison = $bdd->prepare("INSERT INTO series_saisons
	VALUES('',:serie, :saison)");
$reqAddEpisode = $bdd->prepare("INSERT INTO series_episodes
	VALUES(:file, :saison, :nep,'','2013-01-01')");

$reqAllFiles = $bdd->prepare("SELECT id, nom, chemin_complet
	FROM fichiers
	WHERE LOWER(chemin_complet) LIKE '%/serie%' AND supprime = 0 AND type <> 'dossier' AND id NOT IN (SELECT fichier FROM series_episodes)");
$reqAllFiles->execute();
$files = $reqAllFiles->fetchAll();
$reqAllFiles->closeCursor();


$reqAllSeries = $bdd->prepare("SELECT series.id, series.nom, sa.id AS saison, sa.numero
	FROM series
	LEFT JOIN series_saisons AS sa
	ON sa.serie = series.id");
$reqAllSeries->execute();
$seriesAndSa = $reqAllSeries->fetchAll();
$reqAllSeries->closeCursor();

$series = array();
$not = array();
foreach ($seriesAndSa as $s) {
	if (!array_key_exists($s['nom'], $series)) {
		$series[$s['nom']] = array('id' => $s['id']);
	}
	$series[$s['nom']][$s['numero']] = $s['saison'];
}

$total = count($files);
$cinqp = ceil($total / 20);
$last = (-1) * $cinqp * 3;

echo $total . " fichiers à identifier\n\n";
$i = 0;
foreach ($files as $f) {
	$pourc = ceil($i / $total * 100);
	if ($pourc % 10 == 0 and ($i - $last) > $cinqp) {
		$last = $i;
		echo "\n" . $pourc . '%  ';
	}
	$i++;
	
	// On enlève le numéro de la série qui peut être au début du fichier
	if (preg_match('#^[0-9]{1,3}\-#', $f['nom']) and preg_replace('#^([0-9]{1,3})\-.+$#isU', '$1', $f['nom']) != '24') {
    	$f['chemin_complet'] = str_replace($f['nom'], preg_replace('#^([0-9]{1,3})\-(.+)$#isU', '$2', $f['nom']), $f['chemin_complet']);
	}

	$guessit = shell_exec('guessit -a ' . escapeshellarg($f['chemin_complet']));
	$guessit = json_decode(substr($guessit, strpos($guessit, '{')));
	if (isset($guessit->series) and isset($guessit->season) and isset($guessit->episodeNumber)) {
		$nom = $guessit->series->value;
		$nsaison = $guessit->season->value;
		$nep = $guessit->episodeNumber->value;
	} else {
		echo 'n';
		continue;
	}
	//echo $nom . " S" . $nsaison . "E" . $nep . "\n";
	
	
	// La série existe-t-elle en bdd ?
	if (!array_key_exists($nom, $series)) {
		$searchApi = json_decode(file_get_contents('https://api.themoviedb.org/3/search/tv?api_key=10693a5e1e693837a6c36153f260d8d3&query=' . urlencode($nom), false, $cxContext));
		if (count($searchApi->results) > 0) {
			if (levenshtein($nom, strtolower($searchApi->results[0]->original_name)) <= ceil(strlen($nom)*100000)) {
				$infos = $searchApi->results[0];
				$reqAddSerie->bindValue(':nom', $infos->original_name);
				$reqAddSerie->bindValue(':tmdbid', $infos->id);
				$reqAddSerie->bindValue(':tnbseasons', 0);
				$reqAddSerie->bindValue(':tpopularity', $infos->popularity);
				$reqAddSerie->bindValue(':tfirstdate', $infos->first_air_date);
				$reqAddSerie->bindValue(':tlastdate', '2013-01-01');
				$reqAddSerie->bindValue(':tepisode_run_time', 0);
				$reqAddSerie->bindValue(':tgenres', '');
				$reqAddSerie->bindValue(':tin_production', false);
				$reqAddSerie->bindValue(':tnetwork', '');
				$reqAddSerie->bindValue(':torigin_country', '');
				$reqAddSerie->bindValue(':toverview', '');
				$reqAddSerie->execute();
				$series[$nom] = array('id' => $bdd->lastInsertId());
				$reqAddSerie->closeCursor();
				echo 'S';
			} else {
				echo 'C';
				$not[$nom] = true;
				continue;
			}
		} else {
			echo "\n" . $nom . ' -- ' . $f['chemin_complet'] . ' -- ';
			echo 'N';
			$not[$nom] = true;
			continue;
		}
	}
	
	// La saison existe-t-elle en bdd ?
	if (!array_key_exists($nsaison, $series[$nom])) {
		$reqAddSaison->bindValue(':serie', $series[$nom]['id']);
		$reqAddSaison->bindValue(':saison', $nsaison);
		$reqAddSaison->execute();
		$series[$nom][$nsaison] = $bdd->lastInsertId();
		$reqAddSaison->closeCursor();
		echo 's';
	}
	
	
	
	// On ajoute l'épisode
	$reqAddEpisode->bindValue(':file', $f['id']);
	$reqAddEpisode->bindValue(':saison', $series[$nom][$nsaison]);
	$reqAddEpisode->bindValue(':nep', $nep);
	$reqAddEpisode->execute();
	$reqAddEpisode->closeCursor();
	echo 'e';
	
	//echo $nom . " -- " . $f['chemin_complet'] . "\n";
}

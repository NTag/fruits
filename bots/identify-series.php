<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

// SQL
$reqAddSerie = $bdd->prepare("INSERT INTO series
	VALUES('', :nom, :tmdbid)");
$reqAddSaison = $bdd->prepare("INSERT INTO series_saisons
	VALUES('',:serie, :saison)");
$reqAddEpisode = $bdd->prepare("INSERT INTO series_episodes
	VALUES(:file, :saison, :nep)");

$reqAllFiles = $bdd->prepare("SELECT id, nom, chemin_complet
	FROM fichiers
	WHERE LOWER(chemin_complet) LIKE '%/serie%' AND supprime = 0 AND type <> 'dossier' AND id NOT IN (SELECT fichier FROM series_episodes)");
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
	$regex = '#^\W?(([0-9]{2,3}|epz)\W{1,3})?(\w.+)(\W|_){1,3}((s?[0-9]x[0-9]{2})|(S[0-9]{1,2}E[0-9]{2})|(Saison [0-9]{1,2} Episode [0-9]{2})|(Season [0-9]{1,2} Episode [0-9]{2})|([0-9]{2,4})).+$#is';
	$nom = preg_replace($regex, '$4', $f['nom']);
	if ($nom == $f['nom']) {
		echo "\n" . $f['chemin_complet'] . ' -- ';
		echo 'P';
		continue;
	}
	$saison = preg_replace($regex, '$1', $f['nom']);
	if (empty($saison) or strtolower($saison) == 'epz' or strlen($saison) <= 2) {
		$saison = preg_replace($regex, '$5', $f['nom']);
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
	
	
	//echo $nsaison . '/' . $nep . ' ';
	
	$nom = preg_replace('#\.|_#', ' ', $nom);
	$nom = strtolower($nom);
	$nom = preg_replace('# +#isU', ' ', $nom);
	$nom = trim($nom);
	
	// N'a-t-on déjà pas trouvé cette série
	if (array_key_exists($nom, $not)) {
		echo 'D';
		continue;
	}
	
	// La série existe-t-elle en bdd ?
	if (!array_key_exists($nom, $series)) {
		$searchApi = json_decode(file_get_contents('https://api.themoviedb.org/3/search/tv?api_key=10693a5e1e693837a6c36153f260d8d3&query=' . urlencode($nom), false, $cxContext));
		if (count($searchApi->results) > 0) {
			if (levenshtein($nom, strtolower($searchApi->results[0]->original_name)) <= ceil(strlen($nom)*100000)) {
				$reqAddSerie->bindValue(':nom', $searchApi->results[0]->original_name);
				$reqAddSerie->bindValue(':tmdbid', $searchApi->results[0]->id);
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
<?php
require('config.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

echo "# Fruits v5
## Identification des films
## " . date('Y-m-d H:i:s') . "\n";

// SQL
$reqAddFilmF = $bdd->prepare("INSERT INTO filmsf
	VALUES(:file, :tmdbid, :langue, :qualite, :sub)");

$reqAllFiles = $bdd->prepare("SELECT id, nom, chemin_complet
	FROM fichiers
	WHERE LOWER(chemin_complet) LIKE '%/film%' AND supprime = 0 AND type <> 'dossier' AND taille > 2000 AND date_depose > DATE_SUB(NOW(), INTERVAL 2 DAY) AND id NOT IN (SELECT fichier FROM filmsf)");
$reqAllFiles->execute();
$files = $reqAllFiles->fetchAll();
$reqAllFiles->closeCursor();

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
	
	if (in_array(strtolower($f['nom']), $useless)) {
    	continue;
	}

	$guessit = shell_exec('guessit -a ' . escapeshellarg(utf8_decode($f['nom'])));
	$guessit = str_replace('Volap\u00fck', 'VO/VF', $guessit);
	$infosNom = json_decode(substr($guessit, strpos($guessit, '{')));
	$guessit = shell_exec('guessit -a ' . escapeshellarg(utf8_decode($f['chemin_complet'])));
	$guessit = str_replace('Volap\u00fck', 'VO/VF', $guessit);
	$infosChemin = json_decode(substr($guessit, strpos($guessit, '{')));

	if (!isset($infosNom->title)) {
		$infos = $infosChemin;
	} elseif (!isset($infosChemin->title)) {
		$infos = $infosNom;
	} else {
		if ($infosChemin->title->confidence >= $infosNom->title->confidence) {
			$infos = $infosChemin;
		} else {
			$infos = $infosNom;
		}
	}
	
	// Recherche de l'id sur tmdb
	if (isset($infos->year)) {
		$end = '&year=' . $infos->year->value;
	} else {
		$end = '';
	}
	if (!isset($infos->title)) {
		echo $f['chemin_complet'] . "\n";
		echo 'X';
		continue;
	}
	
	$searchApi = json_decode(file_get_contents('https://api.themoviedb.org/3/search/movie?api_key=10693a5e1e693837a6c36153f260d8d3' . $end . '&query=' . urlencode($infos->title->value), false, $cxContext));
	if (count($searchApi->results) == 0 and !empty($end)) {
		$end = '';
		$searchApi = json_decode(file_get_contents('https://api.themoviedb.org/3/search/movie?api_key=10693a5e1e693837a6c36153f260d8d3' . $end . '&query=' . urlencode($infos->title->value), false, $cxContext));
	}
	
	if (count($searchApi->results) > 0) {
		$res = $searchApi->results[0];
		if (isset($infos->language)) {
			$langue = implode(',', $infos->language->value);
		} else {
			$langue = '';
		}
		if (isset($infos->screenSize)) {
			$qualite = $infos->screenSize->value;
		} elseif (isset($infos->format)) {
			$qualite = $infos->format->value;
		} else {
			$qualite = '';
		}
		$sub = (isset($infos->type) and $infos->type->value == 'moviesubtitle');
		$reqAddFilmF->bindValue(':file', $f['id']);
		$reqAddFilmF->bindValue(':tmdbid', $res->id);
		$reqAddFilmF->bindValue(':langue', $langue);
		$reqAddFilmF->bindValue(':qualite', $qualite);
		$reqAddFilmF->bindValue(':sub', $sub);
		$reqAddFilmF->execute();
		$reqAddFilmF->closeCursor();
		echo 'e';
	} else {
		echo $infos->title->value . "\n";
		echo 'n';
	}
}

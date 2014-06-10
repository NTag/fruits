<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

$reqAddFilm = $bdd->prepare("UPDATE films
SET titlefrslug=:titlefrslug
WHERE tmdbid=:tmdbid");

$reqAllId = $bdd->prepare("SELECT tmdbid, titlefr
	FROM films");
$reqAllId->execute();
$films = $reqAllId->fetchAll();
$reqAllId->closeCursor();

$total = count($films);
$cinqp = ceil($total / 20);
$last = (-1) * $cinqp * 3;

echo $total . " films à corriger\n\n";
$i = 0;

foreach ($films as $s) {
	$pourc = ceil($i / $total * 100);
	if ($pourc % 10 == 0 and ($i - $last) > $cinqp) {
		$last = $i;
		echo "\n" . $pourc . '%  ';
	}
	$i++;
	
	$reqAddFilm->bindValue(':titlefrslug', slug($s['titlefr']));
    $reqAddFilm->bindValue(':tmdbid', $s['tmdbid']);
	
	$reqAddFilm->execute();
	$reqAddFilm->closeCursor();
	
	echo ".";
	//exit();
}
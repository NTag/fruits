<?php
require('bdd.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

$f = array('nom' => '30 Rock S03E14 - The Funcooker.avi');
$f['nom'] = str_replace('720p', '', $f['nom']);
$f['nom'] = str_replace('480p', '', $f['nom']);
$f['nom'] = str_replace('1080p', '', $f['nom']);

	$regex = '#^\W?(([0-9]{2,3}|epz)\W{1,3})?(\w.+)(\W|_){1,3}((s?[0-9]{1,2}x[0-9]{2})|(S[0-9]{1,2}E[0-9]{2})|(Saison [0-9]{1,2} Episode [0-9]{2})|(Season [0-9]{1,2} Episode [0-9]{2})).+$#is';
	$nom = preg_replace($regex, '$3', $f['nom']);
	echo $nom;
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
		echo $saison . "\n";
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
		echo 'erreur';
	}
	
	$nsaison = intval($nsaison);
	$nep = intval($nep);
	
	echo $nsaison . "\n" . $nep . "\n";
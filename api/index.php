<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require('config.php');

$app->get('/films', function() use ($app) {
    $films = $app['db']->fetchAll("SELECT tmdbid, title, titlefr, titleen, titlefrslug, YEAR(release_date) AS date, production, popularity, release_date
    FROM films
    ORDER BY popularity DESC");
    return $app->json($films);
});

$app->get('/series', function() use ($app) {
    $series = $app['db']->fetchAll("SELECT id, nom, tmdbid, tfirstdate, tlastdate, tnbseasons, (SELECT COUNT(*) FROM series_saisons AS sa WHERE sa.serie = series.tmdbid) AS nbseasons, tpopularity AS popularity, nom AS title, tfirstdate AS release_date
    FROM series
    ORDER BY tpopularity DESC");
    return $app->json($series);
});

$app->get('/films/{id}', function($id) use ($app) {
    $film = $app['db']->fetchAssoc("SELECT tmdbid, title, titlefr, titleen, overview, genres, budget, popularity, vote, production, release_date, runtime
    FROM films
    WHERE tmdbid = ?", array($id));
    
    $fichiers = $app['db']->fetchAll("SELECT filmsf.fichier, fichiers.chemin_complet, fichiers.serveur, fichiers.nom, fichiers.taille, filmsf.langue, filmsf.qualite, filmsf.sub, fichiers.parent, fichiers.nb_clics
    FROM filmsf
    LEFT JOIN fichiers
    ON fichiers.id = filmsf.fichier
    LEFT JOIN serveurs
    ON serveurs.nom = fichiers.serveur
    WHERE filmsf.tmdbid = ? AND fichiers.supprime = 0 AND serveurs.online=1 AND serveurs.supprime=0 AND (SELECT COUNT(*) FROM ierreurs WHERE ierreurs.fichier = fichiers.id) < 5
    ORDER BY sub ASC, nb_clics DESC", array($id));
    
    $film['fichiers'] = array();
    foreach ($fichiers as $k => $f) {
	    if (!$f['sub']) {
		    $film['fichiers'][] = $f;
		    unset($fichiers[$k]);
	    }
    }
    $film['sub'] = array_values($fichiers);
    
    $film['genres'] = preg_replace('#, $#', '', $film['genres']);
    $film['production'] = preg_replace('#, $#', '', $film['production']);
    
    return $app->json($film);
});

$app->get('/series/{id}', function($id) use ($app) {
    $serie = $app['db']->fetchAssoc("SELECT id, nom, tmdbid, tfirstdate, tlastdate, tnbseasons, tgenres, tin_production, tpopularity, tnetwork, torigin_country, toverview, tepisode_run_time, YEAR(tfirstdate) AS fyear, YEAR(tlastdate) AS lyear, (SELECT COUNT(*) FROM series_saisons AS sa WHERE sa.serie = series.id) AS nbseasons
    FROM series
    WHERE tmdbid = ?", array($id));
    $serie['saisons'] = $app['db']->fetchAll("SELECT id, numero
    FROM series_saisons
    WHERE serie = ?
    ORDER BY numero ASC", array($id));
    
    $serie['tgenres'] = preg_replace('#, $#', '', $serie['tgenres']);
    
    return $app->json($serie);
});

$app->get('/series/saison/{id}', function($id) use ($app) {
    $episodes = $app['db']->fetchAll("SELECT fichier, saison, episode, tname, tdate, chemin_complet, nb_clics, serveur, nom, taille, fichiers.parent
    FROM series_episodes
    LEFT JOIN fichiers
    ON fichiers.id = series_episodes.fichier
    WHERE series_episodes.saison = ?  AND fichiers.supprime = 0 AND (SELECT COUNT(*) FROM ierreurs WHERE ierreurs.fichier = fichiers.id) < 5
    ORDER BY episode ASC", array($id));
    
    $extSubtitles = array(
	    'srt',
	    'sub',
	    'ass',
	    'ssa',
	);
    
    $episodest = array();
    foreach ($episodes as $e) {
	    if (!isset($episodest[$e['episode']])) {
		    $episodest[$e['episode']] = array(
			    'episode' => $e['episode'],
			    'tname' => $e['tname'],
			    'tdate' => $e['tdate'],
			    'ep' => array(),
			    'sub' => array(),
			    );
	    }
	    
	    $ext = pathinfo($e['chemin_complet']);
	    $ext = strtolower($ext['extension']);
	    $type = 'ep';
	    if (in_array($ext, $extSubtitles)) {
		    $type = 'sub';
	    }
	    
	    $episodest[$e['episode']][$type][] = array(
		    'nom' => $e['nom'],
		    'taille' => $e['taille'],
		    'fichier' => $e['fichier'],
		    'chemin_complet' => $e['chemin_complet'],
		    'serveur' => $e['serveur'],
		    'nb_clics' => $e['nb_clics'],
		    'parent' => $e['parent'],
		    );
    }
    
    return $app->json(array_values($episodest));
});

$app->get('/search/{q}', function($q) use ($app) {
    $fichiers = $app['db']->fetchAll("SELECT fichiers.id, fichiers.nom, chemin_complet, fichiers.nb_clics, fichiers.taille, fichiers.serveur, type, parent, (type = 'dossier') AS is_dossier,
    ((MATCH (fichiers.chemin_complet) AGAINST (? IN BOOLEAN MODE)) + (MATCH (fichiers.nom) AGAINST (? IN BOOLEAN MODE))*3)*(nb_clics+1) AS score
    FROM fichiers
    LEFT JOIN serveurs
    ON serveurs.nom=fichiers.serveur
    WHERE serveurs.online=1 AND serveurs.supprime=0 AND MATCH (chemin_complet) AGAINST (? IN BOOLEAN MODE)
    ORDER BY ((MATCH (fichiers.chemin_complet) AGAINST (? IN BOOLEAN MODE)) + (MATCH (fichiers.nom) AGAINST (? IN BOOLEAN MODE))*3)*(nb_clics+1) DESC
    LIMIT 0, 200", array($q, $q, $q, $q, $q));
    
    $app['db']->executeUpdate("INSERT INTO recherches VALUES('',?,?,NOW(),'')", array($q, $_SERVER['REMOTE_ADDR']));
    
    return $app->json(array('fichiers' => $fichiers));
});

$app->get('/serveurs', function() use ($app) {
    $fichiers = $app['db']->fetchAll("SELECT fichiers.id, serveurs.taille, serveur, nb_clics
    FROM serveurs
    LEFT JOIN fichiers
    ON fichiers.serveur=serveurs.nom
    WHERE fichiers.nom='/' AND fichiers.parent IS NULL AND serveurs.supprime=0 AND fichiers.supprime=0 AND online=1
    ORDER BY taille DESC");
    
    return $app->json($fichiers);
});

$app->get('/files/{dir}', function($dir) use ($app) {
	$infos = $app['db']->fetchAssoc("SELECT id, nom, chemin_complet, taille, serveur, type, nb_clics, date_depose, (type='dossier') AS is_dossier, parent
    FROM fichiers
    WHERE id = ?", array($dir));
    $fichiers = $app['db']->fetchAll("SELECT id, nom, chemin_complet, taille, serveur, type, nb_clics, date_depose, (type='dossier') AS is_dossier
    FROM fichiers
    WHERE parent=? AND supprime=0
    ORDER BY (type='dossier') DESC, nom ASC", array($dir));
    $infos['fichiers'] = $fichiers;
    
    return $app->json($infos);
});

$app->get('/files/{file}/click', function($file) use ($app) {
    $app['db']->executeUpdate("UPDATE fichiers SET nb_clics = nb_clics+1 WHERE id=?", array($file));
    
    return $app->json(array('status' => 'ok'));
});

$app->get('/files/{file}/error', function($file) use ($app) {
    $app['db']->executeUpdate("INSERT INTO ierreurs VALUES(?,?,NOW())", array($file, $_SERVER['REMOTE_ADDR']));
    
    return $app->json(array('status' => 'ok'));
});

$app->get('/new', function() use ($app) {
    $fichiers = $app['db']->fetchAll("SELECT files.id, files.nom, files.chemin_complet, files.nb_clics, files.taille, files.serveur, files.type, files.parent, files.is_dossier, files.date_depose FROM (SELECT fichiers.id, fichiers.nom, chemin_complet, fichiers.nb_clics, fichiers.taille, fichiers.serveur, type, parent, (type = 'dossier') AS is_dossier, fichiers.date_depose
    FROM fichiers
    WHERE supprime = 0
    ORDER BY id DESC
    LIMIT 0, 80) AS files
    LEFT JOIN serveurs
    ON serveurs.nom=files.serveur
    WHERE serveurs.online=1 AND serveurs.supprime=0");
    
    return $app->json(array('fichiers' => $fichiers));
});

$app->run();

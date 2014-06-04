<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require('config.php');

$app->get('/films', function() use ($app) {
    $films = $app['db']->fetchAll("SELECT tmdbid, title, titlefr, titleen, YEAR(release_date) AS date, production
    FROM films
    ORDER BY popularity DESC");
    return $app->json($films);
});

$app->get('/series', function() use ($app) {
    $series = $app['db']->fetchAll("SELECT id, nom, tmdbid, tfirstdate, tlastdate, tnbseasons, (SELECT COUNT(*) FROM series_saisons AS sa WHERE sa.serie = series.id) AS nbseasons
    FROM series
    ORDER BY tpopularity DESC");
    return $app->json($series);
});

$app->get('/films/{id}', function($id) use ($app) {
    $film = $app['db']->fetchAssoc("SELECT tmdbid, title, titlefr, titleen, overview, genres, budget, popularity, vote, production, release_date, runtime
    FROM films
    WHERE tmdbid = ?", array($id));
    
    $fichiers = $app['db']->fetchAll("SELECT fichier, chemin_complet, serveur, nom, taille, langue, qualite, sub
    FROM filmsf
    LEFT JOIN fichiers
    ON fichiers.id = filmsf.fichier
    WHERE filmsf.tmdbid = ?
    ORDER BY sub ASC", array($id));
    
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
    WHERE id = ?", array($id));
    $serie['saisons'] = $app['db']->fetchAll("SELECT id, numero
    FROM series_saisons
    WHERE serie = ?
    ORDER BY numero ASC", array($id));
    
    $serie['tgenres'] = preg_replace('#, $#', '', $serie['tgenres']);
    
    return $app->json($serie);
});

$app->get('/series/saison/{id}', function($id) use ($app) {
    $episodes = $app['db']->fetchAll("SELECT fichier, saison, episode, tname, tdate, chemin_complet, serveur, nom, taille
    FROM series_episodes
    LEFT JOIN fichiers
    ON fichiers.id = series_episodes.fichier
    WHERE series_episodes.saison = ?
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
		    );
    }
    
    return $app->json(array_values($episodest));
});

$app->get('/search/{q}', function($q) use ($app) {
    $fichiers = $app['db']->fetchAll("SELECT id, nom, chemin_complet, taille, serveur, type,
    (MATCH (chemin_complet) AGAINST (? IN BOOLEAN MODE))*(nb_clics+1)*0.5 AS score
    FROM fichiers
    WHERE MATCH (chemin_complet) AGAINST (? IN BOOLEAN MODE)
    ORDER BY (MATCH (chemin_complet) AGAINST (? IN BOOLEAN MODE))*(nb_clics+1)*0.5 DESC", array($q, $q, $q));
    
    return $app->json($fichiers);
});

$app->run();

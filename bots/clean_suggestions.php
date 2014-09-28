<?php
require('config.php');
$bdd = new PDO(SMSDSN, SMSUSERNAME, SMSPASSWORD);

echo "\n# Fruits v5
## Nettoyage des suggestions
## " . date('Y-m-d H:i:s') . "\n";

// SQL
$reqFilm = $bdd->prepare("DELETE FROM demandes
WHERE demandes.type = 'movie' AND demandes.tmdbid IN(
SELECT films.tmdbid
    FROM films
    LEFT JOIN filmsf
    ON filmsf.tmdbid = films.tmdbid
    LEFT JOIN fichiers
    ON fichiers.id = filmsf.fichier
    LEFT JOIN serveurs
    ON serveurs.nom = fichiers.serveur
    LEFT JOIN ierreurs
    ON ierreurs.fichier = filmsf.fichier
    WHERE fichiers.supprime = 0 AND serveurs.online=1 AND serveurs.supprime=0
    GROUP BY films.tmdbid
    HAVING COUNT(*)/COUNT(DISTINCT filmsf.fichier) < 5
    ORDER BY popularity DESC)");
$reqSerie = $bdd->prepare("DELETE FROM demandes
LEFT JOIN series
ON series.tmdbid = demandes.tmdbid
WHERE demandes.type = 'tv' AND series.tmdbid IS NOT NULL;");

$reqFilm->execute();
$reqFilm->closeCursor();

$reqSerie->execute();
$reqSerie->closeCursor();

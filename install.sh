#!/bin/bash

echo "# Fruits v5"
echo "## Installation"

echo "### Création des fichiers de configuration"
cp api/config.sample.php api/config.php
cp bots/bdd.sample.php bots/bdd.php

echo "### Création des dossiers de données"
mkdir bots/tvnamer
mkdir bots/tvnamer/files
mkdir api/data
mkdir api/data/films
mkdir api/data/films/poster
mkdir api/data/series
mkdir api/data/series/poster

echo "### Téléchargement et installation de tvnamer et guessit (root requis)"
echo "sudo easy_install tvnamer"
sudo easy_install tvnamer
echo "sudo easy_install guessit"
sudo easy_install guessit
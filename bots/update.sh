#!/bin/bash

echo "Fruits v5 - Mise a jour des donnees"

export http_proxy="http://kuzh.polytechnique.fr:8080"
export https_proxy=$http_proxy

php identify-films.php
php infos-films.php

php identify-series-tvnamer.php
php identify-series-guessit.php
php infos-series.php
php infos-episodes.php

php popularity-update.php

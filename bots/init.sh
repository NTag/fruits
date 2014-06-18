#!/bin/bash

echo "Fruits v5 - Mise a jour des donnees"
php identify-films.php
php infos-films.php

php identify-series-tvnamer.php
php identify-series-guessit.php
php infos-series.php
php infos-episodes.php
php download-images.php

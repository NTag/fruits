-- phpMyAdmin SQL Dump
-- version 4.2.0-rc1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 20 Juin 2014 à 20:23
-- Version du serveur :  5.5.35-0ubuntu0.12.04.2
-- Version de PHP :  5.3.10-1ubuntu3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `fruits`
--

-- --------------------------------------------------------

--
-- Structure de la table `films`
--

CREATE TABLE IF NOT EXISTS `films` (
  `tmdbid` bigint(20) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `titlefr` varchar(150) NOT NULL,
  `titleen` varchar(150) NOT NULL,
  `titlefrslug` varchar(150) NOT NULL,
  `overview` text NOT NULL,
  `genres` tinytext NOT NULL,
  `budget` int(10) unsigned NOT NULL,
  `popularity` float NOT NULL,
  `vote` float NOT NULL,
  `production` tinytext NOT NULL,
  `release_date` date NOT NULL,
  `runtime` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `filmsf`
--

CREATE TABLE IF NOT EXISTS `filmsf` (
  `fichier` int(11) NOT NULL,
  `tmdbid` bigint(20) NOT NULL,
  `langue` tinytext NOT NULL,
  `qualite` varchar(20) NOT NULL,
  `sub` tinyint(1) NOT NULL COMMENT 'Si il s''agit d''un sous-titre ou pas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Correspondance fichier/film';

-- --------------------------------------------------------

--
-- Structure de la table `ierreurs`
--

CREATE TABLE IF NOT EXISTS `ierreurs` (
  `fichier` bigint(20) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
`id` int(10) unsigned NOT NULL,
  `nom` varchar(80) NOT NULL,
  `tmdbid` bigint(20) NOT NULL,
  `tnbseasons` tinyint(2) NOT NULL,
  `tpopularity` float NOT NULL,
  `tfirstdate` date NOT NULL,
  `tlastdate` date NOT NULL,
  `tepisode_run_time` tinyint(4) NOT NULL,
  `tgenres` varchar(80) NOT NULL,
  `tin_production` tinyint(1) NOT NULL,
  `tnetwork` varchar(30) NOT NULL,
  `torigin_country` varchar(10) NOT NULL,
  `toverview` text NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=362 ;

-- --------------------------------------------------------

--
-- Structure de la table `series_episodes`
--

CREATE TABLE IF NOT EXISTS `series_episodes` (
  `fichier` int(10) unsigned NOT NULL,
  `saison` int(10) unsigned NOT NULL,
  `episode` int(11) NOT NULL,
  `tname` varchar(80) NOT NULL,
  `tdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `series_saisons`
--

CREATE TABLE IF NOT EXISTS `series_saisons` (
`id` int(10) unsigned NOT NULL,
  `serie` int(10) unsigned NOT NULL,
  `numero` tinyint(4) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1179 ;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `films`
--
ALTER TABLE `films`
 ADD PRIMARY KEY (`tmdbid`);

--
-- Index pour la table `filmsf`
--
ALTER TABLE `filmsf`
 ADD UNIQUE KEY `fichier` (`fichier`);

--
-- Index pour la table `ierreurs`
--
ALTER TABLE `ierreurs`
 ADD UNIQUE KEY `fichier` (`fichier`,`ip`);

--
-- Index pour la table `series`
--
ALTER TABLE `series`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `imdbid` (`tmdbid`);

--
-- Index pour la table `series_saisons`
--
ALTER TABLE `series_saisons`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=362;
--
-- AUTO_INCREMENT pour la table `series_saisons`
--
ALTER TABLE `series_saisons`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1179;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

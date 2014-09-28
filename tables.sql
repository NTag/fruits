-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 28 Septembre 2014 à 18:21
-- Version du serveur :  5.5.31-1~dotdeb.0
-- Version de PHP :  5.4.30-1~dotdeb.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `megaxload_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresses`
--

CREATE TABLE IF NOT EXISTS `adresses` (
`id` int(11) NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `premiere_connexion` datetime NOT NULL,
  `derniere_connexion` datetime NOT NULL,
  `nb_recherches` bigint(20) NOT NULL DEFAULT '0',
  `nb_votes_0` int(11) NOT NULL DEFAULT '0',
  `nb_votes_1` int(11) NOT NULL DEFAULT '0',
  `nb_votes_2` int(11) NOT NULL DEFAULT '0',
  `nb_votes_3` int(11) NOT NULL DEFAULT '0',
  `nb_votes_4` int(11) NOT NULL DEFAULT '0',
  `nb_votes_5` int(11) NOT NULL DEFAULT '0',
  `nb_commentaires` int(11) NOT NULL DEFAULT '0',
  `nb_details` bigint(20) NOT NULL DEFAULT '0',
  `nb_votes_total` int(11) NOT NULL DEFAULT '0',
  `favourite_server` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1724 ;

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE IF NOT EXISTS `commentaires` (
`id` bigint(20) NOT NULL,
  `id_fichier` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `date_post` datetime NOT NULL,
  `comment` text NOT NULL,
  `supprime` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE IF NOT EXISTS `demandes` (
  `tmdbid` bigint(20) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `tdate` date NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fichiers`
--

CREATE TABLE IF NOT EXISTS `fichiers` (
`id` int(10) unsigned NOT NULL,
  `nom` varchar(255) NOT NULL,
  `chemin_complet` text NOT NULL,
  `serveur` varchar(255) DEFAULT 'thunder',
  `date_depose` datetime DEFAULT NULL,
  `nb_votes_0` int(11) NOT NULL DEFAULT '0',
  `nb_votes_1` int(11) NOT NULL DEFAULT '0',
  `nb_votes_2` int(11) NOT NULL DEFAULT '0',
  `nb_votes_3` int(11) NOT NULL DEFAULT '0',
  `nb_votes_4` int(11) NOT NULL DEFAULT '0',
  `nb_votes_5` int(11) NOT NULL DEFAULT '0',
  `nb_votes_total` int(11) NOT NULL DEFAULT '0',
  `dernier_clic` datetime DEFAULT NULL,
  `nb_clics` int(11) NOT NULL DEFAULT '0',
  `nb_commentaires` int(11) NOT NULL DEFAULT '0',
  `taille` bigint(20) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'fichier',
  `version` bigint(20) NOT NULL DEFAULT '0',
  `supprime` int(11) NOT NULL DEFAULT '0',
  `date_derniere_vue` datetime NOT NULL,
  `parent` varchar(255) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6064747 ;

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
-- Structure de la table `m_albums`
--

CREATE TABLE IF NOT EXISTS `m_albums` (
  `alid` bigint(20) unsigned NOT NULL COMMENT 'id Deezer de l''album',
  `aid` bigint(20) unsigned NOT NULL COMMENT 'id Deezer de l''artiste',
  `title` varchar(50) NOT NULL,
  `release_date` date NOT NULL,
  `record_type` varchar(20) NOT NULL COMMENT 'Album, EP, Single...',
  `nb_tracks` smallint(6) NOT NULL,
  `duration` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `m_artistes`
--

CREATE TABLE IF NOT EXISTS `m_artistes` (
  `aid` bigint(20) unsigned NOT NULL COMMENT 'id Deezer',
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `m_fichiers`
--

CREATE TABLE IF NOT EXISTS `m_fichiers` (
  `fichier` bigint(20) unsigned NOT NULL,
  `mid` bigint(20) NOT NULL COMMENT 'id Deezer/iTunes du morceau'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `m_morceaux`
--

CREATE TABLE IF NOT EXISTS `m_morceaux` (
  `mid` bigint(20) unsigned NOT NULL COMMENT 'id Deezer du morceau',
  `alid` bigint(20) NOT NULL COMMENT 'id Deezer de l''album',
  `title` varchar(50) NOT NULL,
  `duration` smallint(6) NOT NULL,
  `track_position` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `recherches`
--

CREATE TABLE IF NOT EXISTS `recherches` (
`id` bigint(20) NOT NULL,
  `search` varchar(255) DEFAULT NULL,
  `ip` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `server` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=548583 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1076 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3085 ;

-- --------------------------------------------------------

--
-- Structure de la table `serveurs`
--

CREATE TABLE IF NOT EXISTS `serveurs` (
`id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `premiere_vue` datetime NOT NULL,
  `derniere_vue` datetime NOT NULL,
  `taille` bigint(20) NOT NULL DEFAULT '0',
  `nb_elements` bigint(20) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `supprime` int(11) NOT NULL DEFAULT '0',
  `online` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=202 ;

-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
`id` bigint(20) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `fichier` bigint(20) NOT NULL,
  `valeur` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=209 ;

-- --------------------------------------------------------

--
-- Structure de la table `vues`
--

CREATE TABLE IF NOT EXISTS `vues` (
`id` bigint(20) NOT NULL,
  `date` datetime NOT NULL,
  `fichier` bigint(20) NOT NULL,
  `ip` varchar(255) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=213501 ;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `adresses`
--
ALTER TABLE `adresses`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demandes`
--
ALTER TABLE `demandes`
 ADD UNIQUE KEY `tmdbid` (`tmdbid`);

--
-- Index pour la table `fichiers`
--
ALTER TABLE `fichiers`
 ADD PRIMARY KEY (`id`), ADD KEY `ind_nom` (`nom`(20)), ADD KEY `serveur` (`serveur`(20),`chemin_complet`(100)), ADD KEY `index_nb_clics` (`nb_clics`), ADD KEY `index_nb_commentaires` (`nb_commentaires`), ADD KEY `index_taille` (`taille`), ADD KEY `index_date_depose` (`date_depose`), ADD KEY `index_supprime` (`supprime`), ADD KEY `index_nb_votes_total` (`nb_votes_total`), ADD KEY `index_date_derniere_vue` (`date_derniere_vue`), ADD KEY `index_version` (`version`), ADD KEY `index_parent` (`parent`), ADD FULLTEXT KEY `index_serveur` (`serveur`), ADD FULLTEXT KEY `index_nom` (`nom`), ADD FULLTEXT KEY `index_type` (`type`), ADD FULLTEXT KEY `index_chemin_complet` (`chemin_complet`);

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
-- Index pour la table `m_albums`
--
ALTER TABLE `m_albums`
 ADD UNIQUE KEY `did` (`alid`);

--
-- Index pour la table `m_artistes`
--
ALTER TABLE `m_artistes`
 ADD UNIQUE KEY `did` (`aid`);

--
-- Index pour la table `m_fichiers`
--
ALTER TABLE `m_fichiers`
 ADD UNIQUE KEY `fichier` (`fichier`);

--
-- Index pour la table `m_morceaux`
--
ALTER TABLE `m_morceaux`
 ADD UNIQUE KEY `did` (`mid`);

--
-- Index pour la table `recherches`
--
ALTER TABLE `recherches`
 ADD PRIMARY KEY (`id`);

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
-- Index pour la table `serveurs`
--
ALTER TABLE `serveurs`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `votes`
--
ALTER TABLE `votes`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vues`
--
ALTER TABLE `vues`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `adresses`
--
ALTER TABLE `adresses`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1724;
--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT pour la table `fichiers`
--
ALTER TABLE `fichiers`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6064747;
--
-- AUTO_INCREMENT pour la table `recherches`
--
ALTER TABLE `recherches`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=548583;
--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1076;
--
-- AUTO_INCREMENT pour la table `series_saisons`
--
ALTER TABLE `series_saisons`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3085;
--
-- AUTO_INCREMENT pour la table `serveurs`
--
ALTER TABLE `serveurs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=202;
--
-- AUTO_INCREMENT pour la table `votes`
--
ALTER TABLE `votes`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=209;
--
-- AUTO_INCREMENT pour la table `vues`
--
ALTER TABLE `vues`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=213501;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

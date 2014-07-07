'use strict';

/* Controllers */

var fruitsControllers = angular.module('fruitsControllers', []);

fruitsControllers.controller('SeriesListCtrl', ['$scope', '$rootScope', 'Serie', 'Saison',
  function($scope, $rootScope, Serie, Saison) {
    $rootScope.page = 'series';
    $rootScope.rechercher = '';
    document.getElementById('rechercher').focus();
    $scope.focus = false;
    $scope.fep = false;
    $scope.series = Serie.query();
    
    $scope.min = function(a, b) {
	    if (a < b) {
		    return a;
	    } else {
		    return b;
	    }
    };
  }]);
fruitsControllers.controller('SerieCtrl', ['$scope', '$rootScope', 'Serie', 'Saison', '$routeParams',
  function($scope, $rootScope, Serie, Saison, $routeParams) {
    $rootScope.page = 'series';
    $rootScope.rechercher = '';
    $scope.serie = Serie.get({id: $routeParams.id});
    $scope.nsaison = -1;
    
    $scope.affep = function(saison, numero) {
    	if ($scope.nsaison == numero) {
	    	$scope.fep = false;
			$scope.nsaison = -1;
    	} else {
			$scope.episodes = Saison.query({id: saison});
			$scope.fep = true;
			$scope.fepf = false;
			$scope.nsaison = numero;
			$scope.epn = -1;
		}
    };
    $scope.affepf = function(episode, ep, sub) {
    	$scope.epf = ep;
    	$scope.eps = sub;
    	if (episode == $scope.epn) {
	    	$scope.fepf = false;
	    	$scope.epn = -1;
    	} else {
  			$scope.epn = episode;
  			$scope.fepf = true;
		  }
      delete $scope.choixQualite;
      $scope.qualiteChoisie = "none";
    };

    // Retourne des infos concernant les choix possibles
    $scope.preDlSaison = function(episodes) {
      var nbEp = episodes.length;

      // On trouve les plus gros et plus petits fichiers
      var choixQualite = {
        most: {
          taille: 0,
          nb_clics: 0,
          nom: "Défaut",
          ep: []
        },
        min: {
          taille: 0,
          nb_clics: 0,
          nom: "Basse",
          ep: []
        },
        moyen: {
          taille: 0,
          nb_clics: 0,
          nom: "Moyenne",
          ep: []
        },
        max: {
          taille: 0,
          nb_clics: 0,
          nom: "HD",
          ep: []
        }
      }
      for (var i = 0; i < nbEp; i++) {
        episodes[i].min = {
          taille: 999999999999,
          nb_clics: 0,
          id: -1
        };
        episodes[i].max = {
          taille: 0,
          nb_clics: 0,
          id: -1
        };
        episodes[i].most = {
          taille: 0,
          nb_clics: 0,
          id: -1
        };
        for (var j = 0; j < episodes[i].ep.length; j++) {
          if (episodes[i].ep[j].taille > 1000 && (episodes[i].ep[j].taille < (0.9*episodes[i].min.taille)
            || (episodes[i].ep[j].taille < (1.1*episodes[i].min.taille) && episodes[i].ep[j].nb_clics > episodes[i].min.nb_clics))) {
            episodes[i].min = {
              taille: episodes[i].ep[j].taille,
              nb_clics: episodes[i].ep[j].nb_clics,
              id: j
            };
          }
          if (episodes[i].ep[j].taille > (1.1*episodes[i].max.taille)
            || (episodes[i].ep[j].taille > (0.9*episodes[i].max.taille) && episodes[i].ep[j].nb_clics > episodes[i].max.nb_clics)) {
            episodes[i].max = {
              taille: episodes[i].ep[j].taille,
              nb_clics: episodes[i].ep[j].nb_clics,
              id: j
            };
          }
          if (episodes[i].ep[j].nb_clics > episodes[i].most.nb_clics) {
            episodes[i].most = {
              taille: episodes[i].ep[j].taille,
              nb_clics: episodes[i].ep[j].nb_clics,
              id: j
            };
          }
        }
        choixQualite.min.ep.push(episodes[i].ep[episodes[i].min.id]);
        choixQualite.max.ep.push(episodes[i].ep[episodes[i].max.id]);
        choixQualite.most.ep.push(episodes[i].ep[episodes[i].most.id]);
        choixQualite.min.taille += episodes[i].min.taille;
        choixQualite.min.nb_clics += episodes[i].min.nb_clics;
        choixQualite.max.taille += episodes[i].max.taille;
        choixQualite.max.nb_clics += episodes[i].max.nb_clics
        choixQualite.most.taille += episodes[i].most.taille;
        choixQualite.most.nb_clics += episodes[i].most.nb_clics
      }
      console.log(episodes);

      // On cherche des fichiers moyens
      for (var i = 0; i < nbEp; i++) {
        episodes[i].moyen = {
          taille: 0,
          nb_clics: -1,
          id: -1
        };
        for (var j = 0; j < episodes[i].ep.length; j++) {
          if (episodes[i].ep[j].taille > 1000
            && episodes[i].ep[j].taille > (1.5*episodes[i].min.taille)
            && episodes[i].ep[j].taille < (0.8*episodes[i].max.taille)
            && episodes[i].ep[j].nb_clics > episodes[i].moyen.nb_clics) {
            episodes[i].moyen = {
              taille: episodes[i].ep[j].taille,
              nb_clics: episodes[i].ep[j].nb_clics,
              id: j
            };
          }
        }
        if (episodes[i].moyen.id == -1) {
          episodes[i].moyen.taille = episodes[i].most.taille;
          episodes[i].moyen.nb_clics = episodes[i].most.nb_clics;
          episodes[i].moyen.id = episodes[i].most.id;
        }
        choixQualite.moyen.ep.push(episodes[i].ep[episodes[i].moyen.id]);
        choixQualite.moyen.taille += episodes[i].moyen.taille;
        choixQualite.moyen.nb_clics += episodes[i].moyen.nb_clics
      }

      // On regarde s'il y a des qualités à virer
      if ((choixQualite.most.taille > 0.9*choixQualite.min.taille && choixQualite.most.taille < 1.2*choixQualite.min.taille)
        || (choixQualite.most.taille > 0.9*choixQualite.max.taille && choixQualite.most.taille < 1.1*choixQualite.max.taille)
        || (choixQualite.most.taille > 0.9*choixQualite.moyen.taille && choixQualite.most.taille < 1.1*choixQualite.moyen.taille)) {
        delete choixQualite.most;
      }
      if ((choixQualite.moyen.taille > 0.9*choixQualite.min.taille && choixQualite.moyen.taille < 1.2*choixQualite.min.taille)
        || (choixQualite.moyen.taille > 0.9*choixQualite.max.taille && choixQualite.moyen.taille < 1.1*choixQualite.max.taille)) {
        delete choixQualite.moyen;
      }
      if (choixQualite.max.taille < 1.2*choixQualite.min.taille) {
        delete choixQualite.max;
      }
      $scope.choixQualite = choixQualite;
      console.log(choixQualite);
    };
  }]);
fruitsControllers.controller('FilmsListCtrl', ['$scope', '$rootScope', 'Film',
  function($scope, $rootScope, Film) {
    $rootScope.page = 'films';
    $rootScope.rechercher = '';
    document.getElementById('rechercher').focus();
    $scope.fep = false;
    $scope.films = Film.query();
    $scope.loadNb = 120;
    $scope.loadMore = function() {
        $scope.loadNb += 120;
    };
  }]);
fruitsControllers.controller('FilmCtrl', ['$scope', '$rootScope', 'Film', '$routeParams',
  function($scope, $rootScope, Film, $routeParams) {
    $rootScope.rechercher = '';
    $rootScope.page = 'films';
    $scope.film = Film.get({id: $routeParams.id});
  }]);
fruitsControllers.controller('ArtistsListCtrl', ['$scope', '$rootScope', 'Artist',
  function($scope, $rootScope, Artist) {
    $rootScope.page = 'music';
    $rootScope.rechercher = '';
    document.getElementById('rechercher').focus();
    $scope.fep = false;
    $scope.artists = Artist.query();
    $scope.loadNb = 120;
    $scope.loadMore = function() {
        $scope.loadNb += 120;
    };
  }]);
fruitsControllers.controller('ArtistCtrl', ['$scope', '$rootScope', '$sce', 'Artist', '$routeParams',
  function($scope, $rootScope, $sce, Artist, $routeParams) {
    $rootScope.page = 'music';
    $rootScope.rechercher = '';
    $scope.artist = Artist.get({aid: $routeParams.aid});
    $scope.palid = -1;
    $scope.pmid = -1;
    var ltracks;
    $scope.afffiles = function(falid, fmid, ffiles) {
      if (falid == $scope.palid && fmid == $scope.pmid) {
        $scope.palid = -1;
        $scope.pmid = -1;
      } else {
        $scope.palid = falid;
        $scope.pmid = fmid;
      }
      ltracks = ffiles;
      ltracks.forEach(function(t) {
          t.seuil = $rootScope.seuil(); 
        });
      $scope.ftracks = ltracks;
    };
    $scope.lplay = function(ftrack) {
      if ($rootScope.player.mid == ftrack.mid) {
        if ($rootScope.player.play) {
          $rootScope.player.lecteur.pause();
          $rootScope.player.play = false;
        } else {
          $rootScope.player.lecteur.play();
          $rootScope.player.play = true;
        }
      } else {
        var ffile = ftrack.files[0];
        $rootScope.player.lecteur.src="ftp://" + ffile.serveur + ffile.chemin_complet;
        $rootScope.player.lecteur.play();
        $rootScope.player.play = true;
      }
      $rootScope.player.mid = ftrack.mid;
    };
  }]);
  
fruitsControllers.controller('ServeursCtrl', ['$scope', '$rootScope', 'Serveur',
  function($scope, $rootScope, Serveur) {
    $rootScope.page = 'serveurs';
    $rootScope.rechercher = '';
    
    $scope.serveurs = Serveur.query();
  }]);
fruitsControllers.controller('DossierCtrl', ['$scope', '$rootScope', '$routeParams', 'Dossier', 'browser',
  function($scope, $rootScope, $routeParams, Dossier, browser) {
    $rootScope.page = 'serveurs';
    $rootScope.rechercher = '';
    $scope.bDlFolder = browser() == 'chrome';
    
    $scope.dossier = Dossier.get({id: $routeParams.id}, function() {
      $scope.dossier.fichiers.forEach(function(t) {
          t.seuil = $rootScope.seuil(); 
        });
    });
  }]);
fruitsControllers.controller('SearchCtrl', ['$scope', '$rootScope', '$routeParams', 'Search',
  function($scope, $rootScope, $routeParams, Search) {
    $rootScope.page = 'serveurs';
	$scope.searchEnCours = true;
    $scope.search = Search.get({q: $routeParams.q}, function() {
	    $scope.searchEnCours = false;
    });
  }]);
fruitsControllers.controller('NewCtrl', ['$scope', '$rootScope', 'Dossier',
  function($scope, $rootScope, Dossier) {
    $rootScope.page = 'new';
    $rootScope.rechercher = '';
    
    $scope.dossier = Dossier.new();
  }]);

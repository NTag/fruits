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
    $scope.lplay = function(ffile) {
      console.log("ftp://" + ffile.serveur + ffile.chemin_complet);
      //$scope.audio_mp3 = $sce.trustAsResourceUrl("ftp://" + ffile.serveur + ffile.chemin_complet);
      lecteurm.src="ftp://" + ffile.serveur + ffile.chemin_complet;
    };
  }]);
  
fruitsControllers.controller('ServeursCtrl', ['$scope', '$rootScope', 'Serveur',
  function($scope, $rootScope, Serveur) {
    $rootScope.page = 'serveurs';
    $rootScope.rechercher = '';
    
    $scope.serveurs = Serveur.query();
  }]);
fruitsControllers.controller('DossierCtrl', ['$scope', '$rootScope', '$routeParams', 'Dossier',
  function($scope, $rootScope, $routeParams, Dossier) {
    $rootScope.page = 'serveurs';
    $rootScope.rechercher = '';
    
    $scope.dossier = Dossier.get({id: $routeParams.id});
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

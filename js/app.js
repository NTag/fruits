'use strict';

/* App Module */

var fruitsApp = angular.module('fruitsApp', [
  'ngRoute',
  'ngSanitize',
  'angularMoment',
  'infinite-scroll',
  //'fruitsAnimations',

  'fruitsControllers',
  'fruitsFilters',
  'fruitsServices'
]);

fruitsApp.run(function(amMoment) {
    amMoment.changeLanguage('fr');
});

fruitsApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/', {
        redirectTo: '/films'
      }).
      when('/series', {
        templateUrl: 'partials/serie-list.html',
        controller: 'SeriesListCtrl'
      }).
      when('/series/:id', {
        templateUrl: 'partials/serie.html',
        controller: 'SerieCtrl'
      }).
      when('/films', {
        templateUrl: 'partials/film-list.html',
        controller: 'FilmsListCtrl'
      }).
      when('/films/:id', {
        templateUrl: 'partials/film.html',
        controller: 'FilmCtrl'
      }).
      when('/music/artists', {
        templateUrl: 'partials/artists-list.html',
        controller: 'ArtistsListCtrl'
      }).
      when('/music/artists/:aid', {
        templateUrl: 'partials/artist.html',
        controller: 'ArtistCtrl'
      }).
      when('/serveurs', {
        templateUrl: 'partials/serveurs.html',
        controller: 'ServeursCtrl'
      }).
      when('/new', {
        templateUrl: 'partials/fichiers.html',
        controller: 'NewCtrl'
      }).
      when('/dossier/:id', {
        templateUrl: 'partials/fichiers.html',
        controller: 'DossierCtrl'
      }).
      when('/search/:q*', {
        templateUrl: 'partials/search.html',
        controller: 'SearchCtrl'
      }).
      otherwise({
        redirectTo: '/films'
      });
  }]);

fruitsApp.run(function($rootScope, $location, Dossier, browser) {
    $rootScope.search = function() {
        $location.path('/search/' + $rootScope.rechercher);
    }
    $rootScope.clickf = function(file) {
        var cf = Dossier.click({id: file});
    };
    $rootScope.errorf = function(file) {
        var cf = Dossier.error({id: file});
    };
    $rootScope.rsens = function() {
        if ($rootScope.rtri != 'title') {
            return true;
        }
        return false;
    };
    $rootScope.seuil = function() {
	    return Math.random() > 0.3;
    };
    $rootScope.bDlFolder = browser() == 'chrome';
    $rootScope.player = {
      "play": false,
      "mid": -1,
      "lecteur": document.getElementById('lecteurm')
    };
    $rootScope.dlfiles = [];
    $rootScope.imgFtpStates = [];
    $rootScope.downloads = {};
    $rootScope.nbDownloads = 0;
    $rootScope.dlFolder = function(fichiers, nom) {
      var fileArray = fichiers;
      // Suppression des dossiers
      for (var i = fileArray.length - 1; i >= 0; i--) {
        if (fileArray[i].is_dossier == true) {
          fileArray.splice(i, 1);
        }
      }
      if (window.confirm("Les " + fileArray.length + " fichiers vont être téléchargés dans votre dossier de téléchargement habituel. C'est bien ce que vous voulez ?")) {
        $rootScope.dlfiles = $rootScope.dlfiles.concat(fileArray);

        var i = 0;
        var ourid = srandom();
        imgFtpState[ourid] = -1;
        $rootScope.imgFtpStates.push(ourid);
        $rootScope.downloads[ourid] = {
          run: true,
          nbfiles: fileArray.length,
          nom: nom
        };
        $rootScope.nbDownloads++;
        var interval = setInterval(function() {
          if (i < fileArray.length && $rootScope.downloads[ourid].run) {
            var serveur = document.getElementById("dlfi" + fileArray[i].id).dataset.serveur;
            if (serveur == 'esco' || imgFtpState[ourid] >= 3) {
              var clickEvent = document.createEvent("MouseEvent");
              clickEvent.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null); 
              document.getElementById("dlfi" + fileArray[i].id).dispatchEvent(clickEvent);
              for (var j = 0; j < $rootScope.dlfiles.length; j++) {
                if ($rootScope.dlfiles[j].id == fileArray[i].id) {
                  $rootScope.dlfiles.splice(j, 1);
                  break;
                }
              }
              i++;
              $rootScope.downloads[ourid].nbfiles--;
              imgFtpState[ourid] = -1;
            } else if (imgFtpState[ourid] <= -1) {
              if (imgFtpState[ourid] <= -2) {
                imgFtpState[ourid] = -1;
              }
            }
            if (imgFtpState != 0) {
              document.getElementById('imgftp' + ourid).innerHTML = "<img src='ftp://anonymous:anonymous@" + serveur + checkimages[serveur] + "?k=" + srandom() + "' onload='imgFtpState." + ourid + " += 1' onerror='imgFtpState." + ourid + " -= 1' />";
            }
          } else {
              $rootScope.imgFtpStates.splice($rootScope.imgFtpStates.indexOf(ourid), 1);
              delete $rootScope.downloads[ourid];
              $rootScope.nbDownloads--;
              clearInterval(interval);
          }
        },
        1400);
      }
    };
});

function fzero(n) {
  if (n < 10) {
    return "0" + n;
  } else {
    return n;
  }
}
var imgFtpState = {};
function srandom()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 30; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

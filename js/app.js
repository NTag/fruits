'use strict';

/* App Module */

var fruitsApp = angular.module('fruitsApp', [
  'ngRoute',
  'ngSanitize',
  'angularMoment',
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
      when('/serveurs', {
        templateUrl: 'partials/serveurs.html',
        controller: 'ServeursCtrl'
      }).
      otherwise({
        redirectTo: '/films'
      });
  }]);

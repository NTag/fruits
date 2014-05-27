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
        templateUrl: 'partials/list-films.html',
        controller: 'FilmsListCtrl'
      }).
      when('/series', {
        templateUrl: 'partials/serie-list.html',
        controller: 'SeriesListCtrl'
      }).
      when('/series/:id', {
        templateUrl: 'partials/serie.html',
        controller: 'SerieCtrl'
      }).
      otherwise({
        redirectTo: '/series'
      });
  }]);

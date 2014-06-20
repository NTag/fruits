'use strict';

/* Services */

var fruitsServices = angular.module('fruitsServices', ['ngResource']);

fruitsServices.factory('Serie', ['$resource',
  function($resource){
    return $resource('api/series/:id');
  }]);
fruitsServices.factory('Film', ['$resource',
  function($resource){
    return $resource('api/films/:id');
  }]);
fruitsServices.factory('Saison', ['$resource',
  function($resource){
    return $resource('api/series/saison/:id');
  }]);
fruitsServices.factory('Serveur', ['$resource',
  function($resource){
    return $resource('api/serveurs/:id');
  }]);
fruitsServices.factory('Dossier', ['$resource',
  function($resource){
    return $resource('api/files/:id', {},
    {click: {url:'api/files/:id/click'},
     error: {url:'api/files/:id/error'},
     new: {url:'api/new'}});
  }]);
fruitsServices.factory('Search', ['$resource',
  function($resource){
    return $resource('api/search/:q');
  }]);

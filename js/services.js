'use strict';

/* Services */

var fruitsServices = angular.module('fruitsServices', ['ngResource']);

fruitsServices.factory('Serie', ['$resource',
  function($resource){
    return $resource('/fruits/api/series/:id');
  }]);
fruitsServices.factory('Film', ['$resource',
  function($resource){
    return $resource('/fruits/api/films/:id');
  }]);
fruitsServices.factory('Saison', ['$resource',
  function($resource){
    return $resource('/fruits/api/series/saison/:id');
  }]);
fruitsServices.factory('Serveur', ['$resource',
  function($resource){
    return $resource('/fruits/api/serveurs/:id');
  }]);
fruitsServices.factory('Dossier', ['$resource',
  function($resource){
    return $resource('/fruits/api/files/:id', {},
    {click: {url:'/fruits/api/files/:id/click'},
     error: {url:'/fruits/api/files/:id/error'}});
  }]);
fruitsServices.factory('Search', ['$resource',
  function($resource){
    return $resource('/fruits/api/search/:q');
  }]);

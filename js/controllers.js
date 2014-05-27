'use strict';

/* Controllers */

var fruitsControllers = angular.module('fruitsControllers', []);

fruitsControllers.controller('SeriesListCtrl', ['$scope', '$rootScope', 'Serie', 'Saison',
  function($scope, $rootScope, Serie, Saison) {
    $rootScope.page = 'series';
    $scope.focus = false;
    $scope.fep = false;
    $scope.series = Serie.query();
    
    $scope.sfocus = function(sid) {
		$('#list').animatecss('blur-out', 250, function() { });
		$scope.focus = true;
    };
  }]);
fruitsControllers.controller('SerieCtrl', ['$scope', '$rootScope', 'Serie', 'Saison', '$routeParams',
  function($scope, $rootScope, Serie, Saison, $routeParams) {
    $rootScope.page = 'series';
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

/*
phonecatControllers.controller('PhoneDetailCtrl', ['$scope', '$routeParams', 'Phone',
  function($scope, $routeParams, Phone) {
    $scope.phone = Phone.get({phoneId: $routeParams.phoneId}, function(phone) {
      $scope.mainImageUrl = phone.images[0];
    });

    $scope.setImage = function(imageUrl) {
      $scope.mainImageUrl = imageUrl;
    }
  }]);
*/
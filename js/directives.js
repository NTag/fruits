'use strict';

/* Directives */

fruitsApp.directive('keyTrap', function($rootScope, $location) {
  return function(scope, elem) {
    var pages = {1:'films', 2:'series', 3: 'new', 4:'serveurs'};
    var reverse = {'films':1, 'series':2, 'new':3, 'serveurs':4};
    elem.bind('keydown', function(event) {
      if (document.getElementById('rechercher').value.length == 0 || document.activeElement != document.getElementById('rechercher')) {
	      if (event.keyCode == 37) {
		    if ($rootScope.page != 'films') {
		      $location.path('/' + pages[reverse[$rootScope.page]-1]);
	      	}
	      } else if (event.keyCode == 39) {
		      if ($rootScope.page != 'serveurs') {
			      $location.path('/' + pages[reverse[$rootScope.page]+1]);
		      }
		  }
	      scope.$apply();
	  }
    });
  };
});
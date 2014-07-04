'use strict';

/* Filters */

angular.module('fruitsFilters', []).filter('humanSize', function() {
  return function(bytes) {
    var si = true;
    var thresh = si ? 1000 : 1024;
    if(bytes < thresh) return bytes + ' B';
    var units = si ? ['Ko','Mo','Go','To','Po','Eo','Zo','Yo'] : ['Kio','Mio','Gio','Tio','Pio','Eio','Zio','Yio'];
    var u = -1;
    do {
        bytes /= thresh;
        ++u;
    } while(bytes >= thresh);
    return bytes.toFixed(1)+' '+units[u];
  };
}).filter('affZero', function() {
  return function(n) {
    if (parseInt(n) > 9) {
	    return n;
    } else {
	    return "0" + n;
    }
  };
}).filter('ilog', function() {
  return function(score) {
    var n = Math.max(Math.round(Math.log(score)*1.66*2), 0);
    return n;
  };
}).filter('affs', function() {
  return function(nb) {
    if (nb > 1) {
        return "s";
    } else {
        return "";
    }
  };
}).filter('duree', function() {
  return function(n) {
    if (n < 60) {
	    return n + " min";
    }
    if ((n%60) < 10) {
	    var nm = "0" + (n%60);
    } else {
	    var nm = n%60;
    }
    return Math.floor(n/60) + "h" + nm;
  };
}).filter('durees', function() {
  return function(n) {
    if (n < 60) {
      return "00:" + zero(n);
    }
    if (n < 3600 {
      return zero(Math.floor(n/60)) + ":" + zero(Math.floor(n%60));
    }
    return zero(Math.floor(n/3660)) + ":" + zero(Math.floor((n%3660)/60)) + ":" + zero(Math.floor(n%60));
  };
}).filter('stars', function() {
  return function(score) {
    var n = Math.round(score/2);
    var out = "<span class='flashy'>";
    for (var i = 1; i <= n; i++) {
	    out += "&#9734; ";
    }
    out += "</span>";
    for (var i = n+1; i <= 5; i++) {
	    out += "&#9734; ";
    }
    
    return out;
  };
});

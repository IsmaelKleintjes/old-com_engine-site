angular.module("ngLocale", [], ["$provide", function($provide) {
    var PLURAL_CATEGORY = {ZERO: "zero", ONE: "one", TWO: "two", FEW: "few", MANY: "many", OTHER: "other"};
    $provide.value("$locale", {
        "DATETIME_FORMATS": {
            "AMPMS": [
                "AM",
                "PM"
            ],
            "DAY": [
                "zondag",
                "maandag",
                "dinsdag",
                "woensdag",
                "donderdag",
                "vrijdag",
                "zaterdag"
            ],
            "MONTH": [
                "januari",
                "februari",
                "maart",
                "april",
                "mei",
                "juni",
                "juli",
                "augustus",
                "september",
                "oktober",
                "november",
                "december"
            ],
            "SHORTDAY": [
                "zo",
                "ma",
                "di",
                "wo",
                "do",
                "vr",
                "za"
            ],
            "SHORTMONTH": [
                "jan.",
                "feb.",
                "mrt.",
                "apr.",
                "mei",
                "jun.",
                "jul.",
                "aug.",
                "sep.",
                "okt.",
                "nov.",
                "dec."
            ],
            "fullDate": "EEEE d MMMM y",
            "longDate": "d MMMM y",
            "medium": "d MMM y HH:mm:ss",
            "mediumDate": "d MMM y",
            "mediumTime": "HH:mm:ss",
            "short": "dd-MM-yy HH:mm",
            "shortDate": "dd-MM-yy",
            "shortTime": "HH:mm"
        },
        "NUMBER_FORMATS": {
            "CURRENCY_SYM": "\u20ac",
            "DECIMAL_SEP": ",",
            "GROUP_SEP": ".",
            "PATTERNS": [
                {
                    "gSize": 3,
                    "lgSize": 3,
                    "macFrac": 0,
                    "maxFrac": 3,
                    "minFrac": 0,
                    "minInt": 1,
                    "negPre": "-",
                    "negSuf": "",
                    "posPre": "",
                    "posSuf": ""
                },
                {
                    "gSize": 3,
                    "lgSize": 3,
                    "macFrac": 0,
                    "maxFrac": 2,
                    "minFrac": 2,
                    "minInt": 1,
                    "negPre": "\u00a4\u00a0",
                    "negSuf": "-",
                    "posPre": "\u00a4\u00a0",
                    "posSuf": ""
                }
            ]
        },
        "id": "nl-nl",
        "pluralCat": function (n) {  if (n == 1) {   return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;}
    });
}]);



var app = angular.module('app', ['app.controllers']);

// Om IE caching tegen te gaan
app.config(['$httpProvider', function($httpProvider) {
    //initialize get if not there
    if (!$httpProvider.defaults.headers.get) {
        $httpProvider.defaults.headers.get = {};
    }
    //disable IE ajax request caching
    $httpProvider.defaults.headers.get['If-Modified-Since'] = '0';
}]);
// Eind om IE caching tegen te gaan

app.controller('TemplateCtrl', ['$scope', '$window', function ($scope, $window) {
    $scope.getUrlVars = function () {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    };

    $scope.redirect = function(url){
        $window.location = url;
    };

    $scope.safeApply = function(scope) {
        if(!(scope.$$phase || scope.$root.$$phase)){
            scope.$apply();
        }
    }
}]);


app.directive('number', function () {
    return {
        scope: {
            model: '=ngModel'
        },
        require: '?ngModel',
        link: function (scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function (value) {

                if(value != ''){
                    value = value.replace(/\./g, '').replace(/[^0-9/,/.]+/g, '');
                    values = value.split(',');
                    value = values.shift()+(values.length ? ',' + values.join('') : '');
                    value = value == "," ? "" : value;


                    var commaValue = (value.indexOf(',') > -1);

                    var splittedValue = value.split(',');

                    var newFirst = accounting.formatNumber(splittedValue[0], 0, '.');

                    if(commaValue){
                        value = newFirst + ',' + splittedValue[1].substring(0,2);
                    } else {
                        value = newFirst;
                    }

                    ngModel.$viewValue = value;
                    ngModel.$render();
                }

                value = parseFloat(value.toString().replace(/\./g, '').replace(",", "."));

                if(isNaN(value))
                {
                    value = null;
                }

                return value;
            });

            ngModel.$formatters.push(function (value) {
                if(isNaN(parseFloat(value)) || !angular.isDefined(value))
                {
                    return null;
                } else {
                    var number = accounting.formatNumber(value, 2, '.', ',');

                    var splittedValue = number.split(',');

                    if(splittedValue[1] > 0){
                        return splittedValue.join(',');
                    } else {
                        return splittedValue[0];
                    }
                }
            })
        }
    };
});

app.directive('int', function(){
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, modelCtrl) {

            modelCtrl.$parsers.push(function (inputValue) {
                var transformedInput = inputValue ? inputValue.replace(/[^\d.-]/g,'') : null;

                if (transformedInput!=inputValue) {
                    modelCtrl.$setViewValue(transformedInput);
                    modelCtrl.$render();
                }

                return transformedInput;
            });
        }
    };
});

app.directive('tooltip', function(){
    return {
        restrict: 'A',
        link: function(scope, element, attrs){
            $(element).hover(function(){
                // on mouseenter
                $(element).tooltip('show');
            }, function(){
                // on mouseleave
                $(element).tooltip('hide');
            });
        }
    };
});

// Om error tegen te gaan als er geen Angular in een pagina wordt gebruikt.
var app2 = angular.module('app.controllers', []);
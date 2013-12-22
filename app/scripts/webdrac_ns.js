angular.module('webdrac').factory('webdracns', function () {
  return {
    returnUrlTemplate: function ($scope) {
        var $url;
        if (($scope.application !== undefined) && $scope.application.template[0].hasOwnProperty($scope.name)) {
            $url = 'applications/' + $scope.application.name + '/templates/' + $scope.application.template[0][$scope.name].template;
        } else {
            $url = '';
        }
        return $url;
    },
    getName: function ($scope) {
      return $scope.application.name;
    },
    getObjectByName: function ($scope, $name) {
      return $scope.application.objects[$name];

    },
    getColumnsForReport: function ($object, $action) {
      return $object.list[$action].parameters.columns;

    },
    getParametersForObject: function ($object, $action) {
      if ($object.transition[$action] !== undefined) {
        return $object.transition[$action].parameters;
      }
      if ($object.list[$action] !== undefined) {
        return $object.list[$action].parameters;
      }
      return undefined;

    },
    getIntersection: function ($scope,$intersection){
      return $scope.intersection[$intersection];
    },
    getActionsValuesForObject: function ($object, $action, values) {
      if ($object.transition[$action] !== undefined) {
        return $object.transition[$action][values];
      }
      if ($object.list[$action] !== undefined) {
        return $object.list[$action][values];
      }
      return undefined;

    },
    getAttributeForObject: function ($object, $attribute) {
      
      //Get major part of attributes
      var attr = $object.attributes[$attribute];

      //Add name of attribute
      attr.name = $attribute;
      
      return attr;
    },
    reorganizeApplication: function (data) {

      var group = 'manager';
      for (var j = 0; j < data.menu.length; j++) {
        var url = data.menu[j].url;
        // console.log(data.menu[j]);
        if (url !== undefined) {
          //console.log(url);
          try {

            var groups = data.objects[url.object][url.action][url.name].groups;

            data.menu[j].groups = groups;

          } catch (err) {}

          //console.log(groups);
        } else {
          for (var k = 0; k < data.menu[j].menu.length; k++) {
            var suburl = data.menu[j].menu[k].url;
            //  console.log(suburl);
            if (suburl !== undefined) {
              try {
                var groups = data.objects[suburl.object][suburl.action][suburl.name].groups;
                data.menu[j].menu[k].groups = groups;
              } catch (err) {}


            }
          }
        }

      }
      return data;
    }

  };



});

 angular.module('webdrac').factory('webdrac_rest', function($http,$q) {
                return {
                    login : function($user,$pass){
                        var deferred = $q.defer();
                        var promise = $http.get('./largeLoad').success(function (response) {
                            deferred.resolve(response);
                        });
                        // Return the promise to the controller
                        return deferred.promise; 
                    }
                }
            });
 
var INTEGER_REGEXP = /^\-?\d*$/;
angular.module('webdrac').directive('integer', function () {
    return {
        require: 'ngModel',
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue) {
                if (INTEGER_REGEXP.test(viewValue)) {
                    // it is valid
                    ctrl.$setValidity('integer', true);
                    return viewValue;
                }
                // it is invalid, return undefined (no model update)
                ctrl.$setValidity('integer', false);
                return undefined;
            });
        }
    };
});
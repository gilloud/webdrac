'use strict'

v_webdrac = angular.module 'webdrac', [
  'ngCookies',
  'ngSanitize',
  'ngResource',
  'ngRoute',
  'textAngular',
  'jackrabbitsgroup.angular-datetimepicker']
v_webdrac.config ($routeProvider) ->
    $routeProvider
    .when '/', 
        templateUrl: 'views/partials/home.html',
        controller: 'HomeCtrl'
    .when '/login',
        templateUrl: 'views/partials/login.html',
        controller: 'LoginCtrl'
    .when '/logout',
        templateUrl: 'views/partials/logout.html',
        controller: 'LogoutCtrl'
    .when '/:application',
        templateUrl: 'views/partials/home_specific.html',
        controller: 'HomeSpecificCtrl'
    .when '/:application/list/:listId',
        templateUrl: 'views/partials/list.html',
        controller: 'ListCtrl'
    .when '/:application/create/:wfId',
        templateUrl: 'views/partials/create.html',
        controller: 'CreateCtrl'
    .when '/:application/move/:objectId',
        templateUrl: 'views/partials/move.html',
        controller: 'MoveCtrl'
    .when '/:application/:object/:id',
        templateUrl: 'views/partials/object.html',
        controller: 'ObjectCtrl'
    .when '/:application/:object/list/:list',
        templateUrl: 'views/partials/list.html',
        controller: 'ListCtrl'
    .when '/:application/:object/transition/:action',
        templateUrl: 'views/partials/action.html',
        controller: 'ActionCtrl'
    .when '/:application/:object/intersection/:intersection',
        templateUrl: 'views/partials/intersection.html',
        controller: 'IntersectionCtrl'
    .when '/:application/error/:errorId',
        templateUrl: 'views/partials/error.html',
        controller: 'ErrorCtrl'
    .otherwise
        redirectTo: '/:application/error/404'

###
http://stackoverflow.com/questions/19433650/angularjs-and-jqgrid
###
v_webdrac.directive 'ngJqGrid', ->
    restrict: 'E'
    scope: 
      config: '='
      data: '='
    link: (scope, element, attrs) ->
      scope.$watch 'config', (newValue) ->
        element.children().empty()
        table = angular.element '<table id="xxx"></table>'
        element.append table
        $(table).jqGrid newValue
      
        scope.$watch 'data', (newValue, oldValue) ->
          for i in [oldValue..0]
              $(table).jqGrid 'delRowData', i
          if newValue != undefined
            for tmp, i in newValue
                $(table).jqGrid 'addRowData', i, tmp
                null

##-----------------
## source : http://jsfiddle.net/mahbub/GfCTU/19/
v_webdrac.directive 'bsDropdown', ($compile) ->
  restrict: 'E',
  scope:
    items: '=dropdownData',
    doSelect: '&selectVal',
    selectedItem: '=preselectedItem'
    onChange:'=onChange'

  link: (scope, element, attrs) ->
    html = "";
    switch (attrs.menuType)
      when "button" 
        html += '<div class="btn-group"><button class="btn button-label btn-info">-</button><button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
        break;
      else
        html += '<div class="dropdown"><a class="dropdown-toggle" role="button" data-toggle="dropdown"  href="javascript:;">Dropdown<b class="caret"></b></a>';
        break;

    html += '<ul class="dropdown-menu"><li ng-repeat="item in items"><a tabindex="-1" data-ng-click="selectVal(item)">{{item.name}}</a></li></ul></div>';
    element.append($compile(html)(scope));
    for i in scope.items.length
      if scope.items[i].id == scope.selectedItem
        scope.bSelectedItem = scope.items[i];
        break;
      

    scope.selectVal = (item) ->
      if item != undefined
        switch (attrs.menuType)
          when  "button" 
            $('button.button-label', element).html(item.name);
            break;
          else
            $('a.dropdown-toggle', element).html('<b class="caret"></b> ' + item.name);
            break;
        ## http://stackoverflow.com/questions/15339041/how-to-pass-a-function-inside-a-directive-in-angularjs
        scope.$eval(attrs.onChange)
        scope.doSelect {
            selectedVal: item.id
        }
        
    scope.selectVal scope.bSelectedItem

##---------------


v_webdrac.factory "WebDracFactory", ($rootScope,$resource,webdracns) ->
  app = {}
  app.name = ""

  ###
    => Finish caching implementation (with $cacheFactory maybe : http://stackoverflow.com/questions/13012216/angularjs-advanced-caching-techniques)
  ###
  getApplication : (application) ->
    console.log "application : "+application + "<>"+app.name
    
    if app.name == application
      console.log ">>>>>>>>>>>>Application fetched from cache !<<<<<<<<<<<<<"
      $rootScope.$broadcast "appLoaded", app

    ## On ne devrait pas toujour avoir a repasser par le fichier... pourtant si on ne le fait pas, ca ne marche pas :()
    MyApplication = $resource('applications/' + application + '/' + application + '.json');
    MyApplication.get(
        (data) ->
            console.log ">>>>>>>>>>>>Application fetched from FILE !<<<<<<<<<<<<<"
            app = webdracns.reorganizeApplication(data)
            $rootScope.$broadcast "appLoaded", app
        ,(error) ->
            console.log error
            $rootScope.$broadcast "appError", error
            ###
            alert "error during " + application + " loading :" + error + "-" + status
            ###
        )

###
jqgrid_getLink : (cellvalue, options, rowObject ) ->
  return "<a href='/Controller/Action/" + cellvalue + "' >Click here"+cellvalue+"</a>";
jqgrid_unLink: (cellvalue, options, rowObject ) ->
  return "<a href='/Controller/Action/" + cellvalue + "' >Click here22222"+cellvalue+"</a>";
###

jQuery.extend($.fn.fmatter , {
    jqgrid_getLink : (cellvalue, options, rowdata) ->

      return ' '+cellvalue+'<br /><span class="glyphicon glyphicon-circle-arrow-right"></span><a href="/#/'+rowdata._application+'/'+rowdata._entity + '/' + rowdata.id  + '" >Aller a la fiche !</a>';

});

jQuery.extend($.fn.fmatter , {
    jqgrid_level : (cellvalue, options, rowdata) ->

      return cellvalue+"a";

});
jQuery.extend($.fn.fmatter , {
    jqgrid_Selector : (cellvalue, options, rowdata) ->
      return '<select ng-change="alert("aa")"><option>A1</option><option>A2</option><option>A3</option></select>';

});

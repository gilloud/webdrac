'use strict'

angular.module('webdrac')
    .controller 'MainCtrl', ($scope, $route, $routeParams, $location, $http, webdracns,$resource,WebDracFactory) ->
        $scope.baseServerUrl = 'http://localhost/webdrac-git4/api/'
        $scope.$route = $route
        $scope.$location = $location
        $scope.$routeParams = $routeParams
        $scope.application_name = ""

        $scope.authentication = {}
        $scope.authentication.isAuthenticated = false
        $scope.authentication.details = {}
        $scope.authentication.details.username = ""
        $scope.authentication.details.groups = ["all"]


        $scope.setRoute = (route) -> 
            $location.path(route)

        $scope.isLoggedIn  =  -> 
            $scope.authentication.isAuthenticated
           
        ##        $http.get($scope.baseServerUrl+'parameters')

        $http.get('applications/parameters.json')
            .success  (data, status, headers, config) ->
                $scope.parameters = data
            .error  (data, status, headers, config) ->
                alert("error " + data + "-" + status)


        $scope.setApplication  =  (application) ->
            $scope.application_name = application

            MyApplication = $resource('applications/' + application + '/' + application + '.json');

            MyApplication.get(
                (data) ->
                    $scope.application = webdracns.reorganizeApplication(data)
                    WebDracFactory.application = $scope.application
                ,(error) ->
                    alert("error during " + application + " loading :" + error + "-" + status)
                )
            ###
            $http.get('applications/' + application + '/' + application + '.json')
                .success  (data, status, headers, config) ->
                    $scope.application = webdracns.reorganizeApplication(data)
                .error  (data, status, headers, config) ->
                    alert("error during " + application + " loading :" + data + "-" + status)
            ###
        
        $scope.unsetApplication  = -> $scope.application_name = ""

          

        getObjectByName = ($name) -> alert($name)

    .controller 'MenuCtrl', ($scope) ->
        $scope.mythings = ['menu !','menu2']

        $scope.canViewElement = (menu) ->
            if menu.groups == undefined
                return true;
            
            for  menu of menu.groups
                for group of $scope.authentication.details.groups
                    if menu = group
                        return true
            return false

    .controller 'Home', ($scope) ->
        $scope.mythings = ['menu !','menu2']
        
    .controller 'HomeCtrl', ($scope, $routeParams) ->
        $scope.name = "HomeCtrl";
        $scope.params = $routeParams;

    .controller 'LoginCtrl', ($scope, $routeParams, $location, $http,$resource) ->
        $scope.name = "LoginCtrl";
        $scope.params = $routeParams;

        $scope.logIn = ->     
            Authentication = $resource($scope.baseServerUrl+'user/authenticate/:username/:password');
            Authentication.get {username:$scope.username, password:$scope.password},
            (data) -> 
                $scope.authentication.isAuthenticated = true;
                $scope.authentication.details.username = $scope.username;
                $scope.authentication.details.groups = ["administrator", "all", "all_logged", "manager"];
            ,(error) ->
                $scope.message = 'Incorrect credentials';
                $scope.authentication.isAuthenticated = false;
                $scope.authentication.details = {};
                $scope.authentication.details.groups = ['all'];
            $location.path($scope.application_name)


    .controller 'LogoutCtrl', ($scope, $routeParams, $location) ->
        $scope.name = "LogoutCtrl";
        $scope.params = $routeParams;
        $scope.authentication.isAuthenticated = false;
        $scope.authentication.details = {};
        $scope.$parent.unsetApplication();
        $location.path('/');

    .controller 'ErrorCtrl', ($scope, $routeParams) ->
        $scope.name = "ErrorCtrl";
        $scope.params = $routeParams;

        switch parseInt($scope.params.errorId, 10)
            when 404
                $scope.errormsg = "Page not found";
                $scope.technical = "[]";
                break;
            else
                $scope.errormsg = "Unexpected error";


    .controller 'HomeSpecificCtrl',($scope, $routeParams,webdracns,WebDracFactory,$location) ->
        $scope.application = WebDracFactory.getApplication $routeParams.application
        $scope.application_name = $routeParams.application

        $scope.$on "appLoaded", ->
            $scope.name = "HomeSpecificCtrl";
            $scope.params = $routeParams;
            $scope.thetemplate = webdracns.returnUrlTemplate($scope);
        $scope.$on "appError", ->
            $location.path('/');


    .controller 'ListCtrl',($scope, $routeParams, webdracns, $http, $resource,WebDracFactory) ->
        $scope.name = "ListCtrl";
        $scope.params = $routeParams;

        
        WebDracFactory.getApplication $routeParams.application
        
        $scope.application_name = $routeParams.application

        $scope.$on "appLoaded", (events,application) ->
            $scope.application = application
            object = webdracns.getObjectByName($scope, $routeParams.object);

            $scope.list = object.list[$routeParams.list];
            
            DataListResource = $resource($scope.baseServerUrl+'data/:application/:object/:list/',
            {application:'@application', object:'@object',list:'@list'},
            {
            filter: {method:'POST'},
            query: {method:'POST', params:{}, isArray:true}
            });

            obj = {}
            obj.columns = $scope.list.parameters.columns

            obj.filters = $scope.list.parameters.filters
            obj.attributes = webdracns.getObjectByName($scope,$routeParams.object).attributes
            
            console.log obj.attributes
            console.log "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
            ###
            request  = DataListResource.query {application:$routeParams.application
                ,object:$routeParams.object
                ,list:$routeParams.list}, obj
            ,(datas) -> 
                $scope.data = datas
            ###
            console.log "gg", $scope.list
            $scope.config = 
                datatype: "local"
                height: "100%",
                colNames:$scope.list.parameters.columns,
                colModel:$scope.list.parameters.columns_details,
                multiselect: false
            height = $(window).height();
            $('.ui-jqgrid-bdiv').height(height);

            $scope.myFunction = ()->
                console.log('Bonjour !')



    .controller 'ActionCtrl', ($scope,$http, $routeParams, webdracns,$resource,WebDracFactory,$location) ->



        WebDracFactory.getApplication $routeParams.application
        $scope.application_name = $routeParams.application

        $scope.$on "appLoaded", (events,application) ->
            $scope.application = application
            $scope.name = "ActionCtrl";
            $scope.params = $routeParams;
            
            $scope.ObjectName = $routeParams.object;

            object = webdracns.getObjectByName($scope, $routeParams.object);

            $scope.action = object.transition[$scope.ObjectName];

            attributes_name = webdracns.getParametersForObject(object, $routeParams.action).attributes;

            $scope.attributes = []; 



            for k_attribute_name,v_attribute_name of attributes_name
                $scope.attributes.push(webdracns.getAttributeForObject(object, v_attribute_name));

            ###
             Back to the previous page
            ###
            $scope.back = ->
                history.go(-1);

            $scope.submit = (attributes) ->

                data = {};
                data.attributes = attributes;
                data.object = $scope.ObjectName;
                data.application_name = $scope.application_name;

                ObjectToSave = $resource($scope.baseServerUrl+'data/createorupdate');

                ObjectToSave.save data
                ,(ok) ->
                    console.log(ok);
                    alert("all data up to date !");
                    $location.path($routeParams.application+'/'+$routeParams.object+'/'+ok.value);
                ,(error) ->
                    alert("error occured:" + error.msg);
         


    .controller 'ObjectCtrl',($scope,$http, $routeParams,WebDracFactory) ->

        WebDracFactory.getApplication $routeParams.application
        $scope.application_name = $routeParams.application

        $scope.$on "appLoaded", (events,application) ->
            $scope.application = application
            $scope.name = "ObjectCtrl";
            $scope.params = $routeParams;
            $http.post($scope.baseServerUrl+'data/',$scope.params)
                .success (data) ->
                    $scope.attributes = data;
                .error (rtn) ->
                    alert("error occured:" + rtn.msg);


    .controller 'AutoCompleter',($scope,$resource) ->
        $scope.selectUser = (txt) ->
            console.log('music selected!', txt.name+' '+txt.surname,$scope.attruser);
            $scope.attruser = txt.name+' '+txt.surname;
            $scope.attribut.value = txt.id
            console.log 'music selected2',$scope.attruser,txt.id;
            console.log txt;

        $scope.searchUser = (user_text) ->
            console.log('Searching',user_text);

            if(user_text.length>=3)
                UsersFound = $resource($scope.baseServerUrl+'user/find/'+user_text);
                UsersFound.query {},
                    (data) ->
                        console.log "Data found",data
                        $scope.albums = data;
                    ,(error) ->
                        alert("error occured:" + error.msg);
            else
                console.log("please add more caracters !")      
    
    ###
    .controller 'IntersectionCtrl',($scope,$http, $routeParams,WebDracFactory,webdracns) ->

        WebDracFactory.getApplication $routeParams.application
        $scope.application_name = $routeParams.application

        $scope.$on "appLoaded", (events,application) ->
            $scope.application = application
            $scope.name = "IntersectionCtrl";
            $scope.params = $routeParams;
            
            intersection = webdracns.getIntersection application,$routeParams.intersection

            request  = DataListResource.query {application:$routeParams.application
                ,object:$routeParams.object
                ,list:$routeParams.list}, obj
            ,(datas) -> 
                $scope.data = datasu
    ###
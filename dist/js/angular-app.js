/**
 * Created by arafat on 10/25/2016.
 */
var prefix = '';
var GlobalApp = angular.module('GlobalApp', ['angular.filter','ngRoute'], function ($interpolateProvider, $httpProvider,$sceProvider,$routeProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
    $sceProvider.enabled(false)
    $httpProvider.useApplyAsync(true)
    var retryCount = 0;
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
    $httpProvider.interceptors.push(function ($q, $injector) {
        return {
            response: function (response) {
                if (response.data.status == 'logout') {
                    location.assign(response.data.loc);
                    return;
                }
                else if (response.data.status == 'forbidden') {

                }
                return response;
            },
            responseError: function (response) {
                console.log(response);
//                        var a = response;
                switch (response.status) {
                    case 404:
                        response.data = "Not found(404)"
                        break;
                    case 500:
                        var d = $q.defer();
                        if (retryCount < 4) {
                            retryHttpRequest(response.config, d);
                            return d.promise;
                        }
                        retryCount = 0;
                        break;
                }
                return $q.reject(response);
            }
        }
        function retryHttpRequest(config, deferred) {
            retryCount++;
            function successCallback(response) {
                deferred.resolve(response);
            }

            function errorCallback(response) {
                deferred.reject(response);
            }

            var $http = $injector.get('$http');
            $http(config).then(successCallback, errorCallback);
        }
    })
    $routeProvider.when('/withdraw/:id',{
        templateUrl:'/'+prefix+'HRM/kpi-withdraw-action-view',
        controller:'WithdrawActionController',
        resolve:{
            kpiInfo: function ($http,$route) {
                return $http.get('/'+prefix+'HRM/kpiinfo/'+$route.current.params.id).then(function (response) {
                    return response.data;
                });
            }
        }
    }).otherwise({
        redirectTo:'/'
    })

});
GlobalApp.filter('dateformat', function () {
    return function (input,format) {
        return moment(input).format(format);
    }
})
GlobalApp.directive('showAlert', function () {
    return {
            restrict: 'AEC',
            scope: {
                alerts: "=",
                close: "&"
            },
            templateUrl: 'dist/template/alert_template.html'
}
})
GlobalApp.directive('templateList',function(){
    return {
        restrict:'AE',
        scope:{
            data:'=',
            dateFormat:'&'
        },
        templateUrl: function (elem, attrs) {

            return '/'+prefix+'HRM/template_list/'+attrs.key
        }
    }
})
GlobalApp.factory('httpService', function ($http) {
    return{
        range: function () {
            return $http({
                method:'get',
                url:'/'+prefix+'HRM/DivisionName'
            }).then(function (response) {
                return response.data
            })

        },
        unit: function (id) {
            var http = '';
            if(id==undefined){
                http = $http({
                    method:'get',
                    url:'/'+prefix+'HRM/DistrictName'
                })
            }
            else{
                http = $http({
                    method:'get',
                    url:'/'+prefix+'HRM/DistrictName',
                    params:{id:id}
                })
            }
            return http.then(function (response) {
                return response.data
            })

        },
        thana: function (id) {
            var http = '';
            if(id==undefined){
                http = $http({
                    method:'get',
                    url:'/'+prefix+'HRM/ThanaName'
                })
            }
            else{
                http = $http({
                    method:'get',
                    url:'/'+prefix+'HRM/ThanaName',
                    params:{id:id}
                })
            }
            return http.then(function (response) {
                return response.data
            })

        },
    }
})
GlobalApp.factory('notificationService', function () {
    return{
        notify: function (type,message) {
            $.noty.closeAll();
            noty({
                type:type,
                text:message,
                layout:'top',
                maxVisible:1,
                timeout:3000
            })

        }
    }
})
GlobalApp.controller('WithdrawActionController', function ($scope,$http,kpiInfo,$routeParams,$location,notificationService) {
    $scope.info = kpiInfo;
    //alert(id)
    $scope.isSubmitting = false;
    $scope.formData = {};
    $scope.submitForm = function () {
        $scope.isSubmitting = true;
        $scope.error = undefined
        $http({
            url:'/'+prefix+'HRM/kpi-withdraw-update/'+$routeParams.id,
            method:'post',
            data:angular.toJson($scope.formData)
        }).then(function (response) {
            $scope.isSubmitting = false;
            console.log(response.data)
            if(response.data.status){
                $location.path('/')
                notificationService.notify('success',response.data.message);
                $scope.$parent.loadTotal()
            }
            else{
                notificationService.notify('error',response.data.message);
            }
        }, function (response) {
            $scope.isSubmitting = false;
            if(response.status==422)$scope.error = response.data;
        })
    }
})

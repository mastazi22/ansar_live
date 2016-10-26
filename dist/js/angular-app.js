/**
 * Created by arafat on 10/25/2016.
 */
var prefix = '';
var GlobalApp = angular.module('GlobalApp', ['angular.filter'], function ($interpolateProvider, $httpProvider,$sceProvider) {
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

/**
 * Created by arafat on 10/25/2016.
 */
var prefix = '';
var GlobalApp = angular.module('GlobalApp', ['angular.filter', 'ngRoute'], function ($interpolateProvider, $httpProvider, $sceProvider, $routeProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
    //$sceProvider.enabled(false)
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
    $routeProvider.when('/withdraw/:id', {
        templateUrl: '/' + prefix + 'HRM/kpi-withdraw-action-view',
        controller: 'WithdrawActionController',
        resolve: {
            kpiInfo: function ($http, $route) {
                return $http.get('/' + prefix + 'HRM/kpiinfo/' + $route.current.params.id).then(function (response) {
                    return response.data;
                });
            }
        }
    }).otherwise({
        redirectTo: '/'
    })

    //console.log(this)

}).run(function ($rootScope,$http) {
    $rootScope.user = ''
    $http.get('/'+prefix+'user_data').then(function (response) {
        $rootScope.user = response.data;
    })
    $rootScope.loadingView = false;
});
GlobalApp.filter('dateformat', function () {
    return function (input, format) {
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
GlobalApp.directive('templateList', function () {
    return {
        restrict: 'AE',
        scope: {
            data: '=',
            dateFormat: '&'
        },
        templateUrl: function (elem, attrs) {

            return '/' + prefix + 'HRM/template_list/' + attrs.key
        }
    }
})
GlobalApp.directive('confirm', function () {
    return {
        restrict: 'A',
        scope: {
            callback: '&',
            data: '=',
            message: '@',
            event: '@'
        },
        link: function (scope, element, attrs) {
            //alert(scope.event)
            $(element).confirmDialog({
                message: scope.message,
                ok_button_text: 'Confirm',
                cancel_button_text: 'Cancel',
                event: scope.event,
                ok_callback: function (element) {
                    scope.callback(scope.data)
                },
                cancel_callback: function (element) {
                }
            })

        }
    }
})
GlobalApp.directive('datePicker', function () {
    return {
        restrict: 'AC',
        link: function (scope, element, attrs) {
            //alert(scope.event)
            $(element).datePicker()

        }
    }
})
GlobalApp.directive('modal', function () {
    return {
        restrict: 'A',
        scope: {
            show: '&',
            hide: '&'
        },
        link: function (scope, element, attrs) {
            //alert(scope.event)
            $(element).on('hide.bs.modal', function () {
                scope.hide();
            })
            $(element).on('hide.bs.show', function () {
                scope.show()
            })

        }
    }
})
GlobalApp.factory('httpService', function ($http) {

    return {
        range: function () {
            return $http({
                method: 'get',
                url: '/' + prefix + 'HRM/DivisionName'
            }).then(function (response) {
                return response.data
            })

        },
        unit: function (id) {
            var http = '';
            if (id == undefined) {
                http = $http({
                    method: 'get',
                    url: '/' + prefix + 'HRM/DistrictName'
                })
            }
            else {
                http = $http({
                    method: 'get',
                    url: '/' + prefix + 'HRM/DistrictName',
                    params: {id: id}
                })
            }
            return http.then(function (response) {
                return response.data
            })

        },
        thana: function (id) {
            var http = '';
            if (id == undefined) {
                http = $http({
                    method: 'get',
                    url: '/' + prefix + 'HRM/ThanaName'
                })
            }
            else {
                http = $http({
                    method: 'get',
                    url: '/' + prefix + 'HRM/ThanaName',
                    params: {id: id}
                })
            }
            return http.then(function (response) {
                return response.data
            })

        },
        kpi: function (id) {
            return $http({
                url:'/'+prefix+"HRM/KPIName",
                method:'get',
                params:{id:id}
            }).then(function (response) {
                return response.data;
            })
        },
        rank: function () {
            return $http({
                url: '/' + prefix + 'HRM/ansar_rank',
                method: 'get'
            }).then(function (response) {
                return response.data;
            })
        },
        disease: function () {
            return $http.get('/'+prefix+'HRM/getDiseaseName').then(function (response) {
                return response.data;
            })
        },
        skill: function () {
            return $http.get('/'+prefix+'HRM/getallskill')
        },
        education: function () {
            return $http.get('/'+prefix+'HRM/getalleducation').then(function (response) {
                return response.data;
            })
        },
        bloodGroup: function () {
            return $http.get('/'+prefix+'HRM/getBloodName').then(function (response) {
                return response.data;
            })
        }
    }
})
GlobalApp.factory('notificationService', function () {
    return {
        notify: function (type, message) {
            //$.noty.closeAll();
            noty({
                type: type,
                text: message,
                layout: 'top',
                maxVisible: 5,
                timeout: 5000,
                dismissQueue: true
            })

        }
    }
})
GlobalApp.controller('WithdrawActionController', function ($scope, $http, kpiInfo, $routeParams, $location, notificationService) {
    $scope.info = kpiInfo;
    //alert(id)
    $scope.isSubmitting = false;
    $scope.formData = {};
    $scope.submitForm = function () {
        $scope.isSubmitting = true;
        $scope.error = undefined
        $http({
            url: '/' + prefix + 'HRM/kpi-withdraw-update/' + $routeParams.id,
            method: 'post',
            data: angular.toJson($scope.formData)
        }).then(function (response) {
            $scope.isSubmitting = false;
            console.log(response.data)
            if (response.data.status) {
                $location.path('/')
                notificationService.notify('success', response.data.message);
                $scope.$parent.loadTotal()
            }
            else {
                notificationService.notify('error', response.data.message);
            }
        }, function (response) {
            $scope.isSubmitting = false;
            if (response.status == 422)$scope.error = response.data;
        })
    }
})
GlobalApp.directive('filterTemplate', function ($timeout,$rootScope) {
    $rootScope.loadingView = true;
    return {
        restrict:'E',
        scope:{
            showItem:'@',// ['range','unit','thana','kpi']
            rangeChange:'&',//{func()}
            unitChange:'&',//{func()}
            thanaChange:'&',//{func()}
            kpiChange:'&',//{func()}
            rangeLoad:'&',//{func()}
            unitLoad:'&',//{func()}
            thanaLoad:'&',//{func()}
            kpiLoad:'&',//{func()}
            onLoad:"&",
            type:'@',//['all','single']
            startLoad:'@',//['range','unit','thana','kpi']
            fieldWidth:'=',
            data:'='
        },
        controller: function ($scope,$rootScope,httpService) {
            $scope.selected = {
                range:$scope.type=='all'?'all':'',
                unit:$scope.type=='all'?'all':'',
                thana:$scope.type=='all'?'all':'',
                kpi:$scope.type=='all'?'all':'',
            }
            $scope.finish = false;
            $scope.loading = {
                range:false,
                unit:false,
                thana:false,
                kpi:false,
            }
            $scope.show = function (item) {
                return $scope.showItem.indexOf(item)>-1&&hasPermission(item);
            }
            function hasPermission(item){
                if(!$rootScope.user) return false;
                if($rootScope.user.usertype.type_name=='DC'&&(item=='range'||item=='unit')){
                    return false;
                }
                else if($rootScope.user.usertype.type_name=='RC'&&item=='range'){
                    return false;
                }
                else if($rootScope.user.usertype.type_name=='Checker'||$rootScope.user.usertype.type_name=='Dataentry'){
                    return false;
                }
                else{
                    return true;
                }
            }
            $scope.loadRange = function () {

                if(!$scope.show('range')) return;
                $scope.loading.range = true;
                httpService.range().then(function (data) {
                    $scope.ranges = data;
                    $scope.loading.range = false;
                })
                $scope.rangeLoad({param:$scope.selected});
            }
            $scope.loadUnit = function (id) {
                if(!$scope.show('unit')) return;
                $scope.units = $scope.thanas = $scope.kpis = []
                $scope.loading.unit = true;
                httpService.unit(id).then(function (data) {
                    $scope.units = data;
                    $scope.loading.unit = false;
                })
                $scope.unitLoad({param:$scope.selected});
            }
            $scope.loadThana = function (id) {
                if(!$scope.show('thana')) return;
                $scope.thanas = $scope.kpis = []
                $scope.loading.thana = true;
                httpService.thana(id).then(function (data) {
                    $scope.thanas = data;
                    $scope.loading.thana = false;
                })
                $scope.thanaLoad({param:$scope.selected});
            }
            $scope.loadKPI = function (id) {

                //$scope.kpiChange({param:$scope.selected});
                if(!$scope.show('kpi')) return;
                $scope.loading.kpi = true;
                httpService.kpi(id).then(function (data) {
                    $scope.kpis = data;
                    $scope.loading.kpi = false;

                })
                $scope.kpiLoad({param:$scope.selected});
            }
            $rootScope.$watch('user', function (n,o) {
                if(!n) return;
                if($rootScope.user.usertype.type_name=='DC'){
                    $scope.selected.range = $rootScope.user.district.division_id
                    $scope.selected.unit = $rootScope.user.district.id
                    $scope.loadThana($rootScope.user.district.id)
                }
                else if($rootScope.user.usertype.type_name=='RC'){
                    $scope.selected.range = $rootScope.user.division.id
                    $scope.loadUnit($rootScope.user.division.id)
                }
                else if($rootScope.user.usertype.type_name=='Admin'||$rootScope.user.usertype.type_name=='DG'){
                    switch($scope.startLoad){
                        case 'range':
                            $scope.loadRange();
                            break;
                        case 'unit':
                            $scope.loadUnit();
                            break;
                        case 'thana':
                            $scope.loadThana();
                            break;
                        case 'kpi':
                            $scope.loadKpi();
                            break;

                    }
                }
                $scope.finish = true;
            })

        },
        templateUrl:'/' + prefix + 'HRM/template_list/range_unit_thana_kpi_rank_gender_template',
        link: function (scope,element,attrs) {
            //alert("aise")
            $rootScope.loadingView = false;
            scope.data = scope.selected;
            $timeout(function () {
                scope.$watch('finish', function (n, o) {
                    if(n)  scope.onLoad({param:scope.selected});
                })

            })

            $(element).on('change',"#range", function () {
                //alert('aSsas')
                scope.selected.unit = scope.type=='all'?'all':''
                scope.selected.thana = scope.type=='all'?'all':''
                scope.selected.kpi = scope.type=='all'?'all':''
                scope.rangeChange({param:scope.selected})
            })
            $(element).on('change','#unit', function () {
                scope.selected.thana = scope.type=='all'?'all':''
                scope.selected.kpi = scope.type=='all'?'all':''
                scope.unitChange({param:scope.selected})
            })
            $(element).on('change',"#thana", function () {
                scope.selected.kpi = scope.type=='all'?'all':''
                scope.thanaChange({param:scope.selected})
            })
            $(element).on('change',"#kpi", function () {
                scope.kpiChange({param:scope.selected})
            })
        }
    }
})
GlobalApp.directive('tableSearch',function () {
    return{
        restrict:'ACE',
        template:'<span class="text text-bold" style="color:black;font-size:1.1em">Total Ansars : [[results==undefined?0:results.length]]</span> <input type="text" class="pull-right"  name="" id="" ng-model="q" placeholder="Search Ansar in this table">',
        scope:{
            q:'=',
            results:'='
        }
    }
})
GlobalApp.directive('databaseSearch',function () {
    return{
        restrict:'ACE',
        template:'<input type="text" ng-model="q" class="form-control" style="margin-bottom: 10px" ng-change="queue.push(1)" placeholder="[[placeHolder?placeHolder:\'Search by Ansar id\']]">',
        scope:{
            queue:'=',
            q:'=',
            placeHolder:'@',
            onChange:'&'
        },
        controller:function ($scope) {
            $scope.$watch('queue',function (n, o) {
                //alert(n)
                if(n.length===1) {
                    $scope.onChange();
                }
                
            },true)
        }
    }
})

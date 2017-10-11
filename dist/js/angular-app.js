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
    $httpProvider.interceptors.push(function ($q, $injector, notificationService) {
        return {
            response: function (response) {
                if (response.data.status == 'logout') {
                    location.assign(response.data.loc);
                    return;
                }
                else if (response.data.status == 'forbidden') {

                }
                else if (response.data.type == 'export') {
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                    }
                    else {
                        notificationService.notify('error', response.data.message);
                    }
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

}).run(function ($rootScope, $http, $window) {

    var ws;
    $rootScope.user = ''
    $http.get('/' + prefix + 'user_data').then(function (response) {
        $rootScope.user = response.data;
        /*ws = openSocketConnection($rootScope.user.id);
        var p = setInterval(function () {
            console.log(ws)
            if(ws.readyState===3) clearInterval(p)
            if(ws.readyState===1&&ws.bufferedAmount===0){
                var uid = $cookies.get('uid')
                if(!uid){
                    uid = 'id'+(new Date()).getTime();
                    $cookies.put('uid',uid);
                }
                ws.send(JSON.stringify({'user': $rootScope.user.id,'uid':uid}))
                clearInterval(p)
            }
        },500)*/
    })
    $rootScope.loadingView = false;
    $rootScope.dateConvert = function (date) {
        return (moment(date).locale('bn').format('DD-MMMM-YYYY'));
    }
    /*var windowElement = angular.element($window);
    windowElement.on('beforeunload', function (event) {

        if (ws) ws.close();
    });*/

    /*function openSocketConnection() {
        var ws = new WebSocket("ws://" + window.location.hostname + ":8090/");
        ws.onopen = function (event) {
            console.log(event)

        }
        ws.onerror = function (event) {
            console.log(event)
        }
        ws.onmessage = function (event) {
            noty({
                text: event.data,
                layout:'bottomRight',
                type:'success'
            })
        }
        ws.onclose = function (event) {
            console.log(event)

        }
        return ws;

    }*/
});
GlobalApp.filter('num', function () {
    return function (input,defaultValue) {
        var d = parseInt(input===undefined?'':input.replace(',', ''));
        return isNaN(d) ? defaultValue==undefined?'':defaultValue : d;
    };
});
GlobalApp.filter('checkpermission', function ($rootScope) {
    return function (input, type1, type2, type3) {
        console.log(type1 + " " + type2 + " " + type3)
        try {
            var permissions = JSON.parse($rootScope.user.user_permission.permission_list);
            return permissions.indexOf(type1) >= 0 || $rootScope.user.usertype.type_code == 11 || $rootScope.user.embodiment.indexOf(type3) >= 0 ? input : "";
        } catch (e) {
            console.error(e)
            return input;
        }
    };
});
GlobalApp.filter('dateformat', function () {
    return function (input, format, local) {
        if (local == undefined) local = 'en'
        return moment(input).locale(local).format(format);
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
            dateFormat: '&',
            callBack: '&',
        },
        templateUrl: function (elem, attrs) {

            return '/' + prefix + 'HRM/template_list/' + attrs.key
        },
        controller: function ($scope) {
            $scope.loadPage = function () {
                $scope.callBack();
            }

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
            var data = attrs.datePicker
            console.log(data)
            if (data) {
                $(element).val(eval(data));

            }

            $(element).datepicker({
                dateFormat:'dd-M-yy'
            })

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
GlobalApp.directive('modalShow', function ($timeout) {
    return {
        restrict: 'A',
        scope: {
            data: '=',
            callback: '&',
            target: '@'
        },
        link: function (scope, element, attrs) {
            //alert(scope.event)
            $(element).on('click', function () {
                scope.callback({data: scope.data});
                $timeout(function () {
                    scope.$apply();
                    $(scope.target).modal('show')
                })
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
            }, function (response) {
                return response
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
            }, function (response) {
                return response
            })

        },
        thana: function (division, id) {
            var http = '';
            //if (id == undefined) {
            //    http = $http({
            //        method: 'get',
            //        url: '/' + prefix + 'HRM/ThanaName'
            //    })
            //}
            //else {
            http = $http({
                method: 'get',
                url: '/' + prefix + 'HRM/ThanaName',
                params: {id: id, division_id: division}
            })
            //}
            return http.then(function (response) {
                return response.data
            }, function (response) {
                return response
            })

        },
        kpi: function (d, u, id, type) {
            return $http({
                url: '/' + prefix + "HRM/KPIName",
                method: 'get',
                params: {division: d, unit: u, id: id, type: type}
            }).then(function (response) {
                return response.data;
            }, function (response) {
                return response
            })
        },
        rank: function () {
            return $http({
                url: '/' + prefix + 'HRM/ansar_rank',
                method: 'get'
            }).then(function (response) {
                return response.data;
            }, function (response) {
                return response
            })
        },
        disease: function () {
            return $http.get('/' + prefix + 'HRM/getDiseaseName').then(function (response) {
                return response.data;
            })
        },
        skill: function () {
            return $http.get('/' + prefix + 'HRM/getallskill')
        },
        education: function () {
            return $http.get('/' + prefix + 'HRM/getalleducation').then(function (response) {
                return response.data;
            })
        },
        bloodGroup: function () {
            return $http.get('/' + prefix + 'HRM/getBloodName').then(function (response) {
                return response.data;
            })
        },
        category: function (data) {
            return $http({
                url:'/' + prefix + 'recruitment/category',
                method:'get',
                params:data
            })
        },
        circular: function (data) {
            return $http({
                url:'/' + prefix + 'recruitment/circular',
                method:'get',
                params:data
            })
        },
        circularSummery: function (data) {
            return $http({
                url:'/' + prefix + 'recruitment/applicant',
                method:'post',
                data:data
            })
        },
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
            if (response.status == 422) $scope.error = response.data;
        })
    }
})
GlobalApp.directive('filterTemplate', function ($timeout, $rootScope) {
    $rootScope.loadingView = true;
    return {
        restrict: 'E',
        scope: {
            showItem: '@',// ['range','unit','thana','kpi']
            rangeChange: '&',//{func()}
            unitChange: '&',//{func()}
            thanaChange: '&',//{func()}
            kpiChange: '&',//{func()}
            rankChange: '&',//{func()}
            genderChange: '&',//{func()}
            rangeLoad: '&',//{func()}
            unitLoad: '&',//{func()}
            thanaLoad: '&',//{func()}
            kpiLoad: '&',//{func()}
            kpiDisabled: '=?',
            rangeDisabled: '=?',
            thanaDisabled: '=?',
            unitDisabled: '=?',
            kpiFieldDisabled: '=?',
            unitFieldDisabled: '=?',
            rangeFieldDisabled: '=?',
            thanaFieldDisabled: '=?',
            loadWatch: '=?',
            watchChange: '@',
            onLoad: "&",
            type: '@',//['all','single']
            startLoad: '@',//['range','unit','thana','kpi']
            fieldWidth: '=?',
            fieldName: '=?',
            layoutVertical: '@',
            getKpiName: '=?',
            getUnitName: '=?',
            getThanaName: '=?',
            data: '=?',
            errorKey: '=?',
            reset: '=?',
            errorMessage: '=?',
            customField: '=?',
            customLabel: '@',
            customData: '=?',
            customModel: '=?',
            customChange: '&',
            kpiType: '@',
            resetAll: '@'
        },
        controller: function ($scope, $rootScope, httpService) {
            $scope.selected = {
                range: $scope.type == 'all' ? 'all' : '',
                unit: $scope.type == 'all' ? 'all' : '',
                thana: $scope.type == 'all' ? 'all' : '',
                kpi: $scope.type == 'all' ? 'all' : '',
                rank: $scope.type == 'all' ? 'all' : '',
                gender: $scope.type == 'all' ? 'all' : '',
                custom: $scope.customModel
            }
            $scope.$watch('resetAll', function (n, o) {
                n = (n === 'true')
                //alert(typeof n)
                if (n === true) {
                    //alert("aise")
                    $scope.selected = {
                        range: $scope.type == 'all' ? 'all' : '',
                        unit: $scope.type == 'all' ? 'all' : '',
                        thana: $scope.type == 'all' ? 'all' : '',
                        kpi: $scope.type == 'all' ? 'all' : '',
                        rank: $scope.type == 'all' ? 'all' : '',
                        gender: $scope.type == 'all' ? 'all' : '',
                        custom: $scope.customModel
                    }
                    if ($rootScope.user.usertype.type_name === 'DC') {
                        $scope.kpis = [];
                    }
                    else if ($rootScope.user.usertype.type_name === 'RC') {
                        $scope.kpis = [];
                        $scope.thanas = [];
                    }
                    else {
                        $scope.kpis = [];
                        $scope.thanas = [];
                        $scope.units = [];
                    }
                    //$scope.$apply()
                }
            })
            $scope.genders = [
                {value: 'Male', text: 'Male'},
                {value: 'Female', text: 'Female'},
                {value: 'Other', text: 'Other'},
            ]
            $scope.finish = false;
            $scope.loading = {
                range: false,
                unit: false,
                thana: false,
                kpi: false,
            }
            $scope.show = function (item) {
                return $scope.showItem.indexOf(item) > -1 && hasPermission(item);
            }

            function hasPermission(item) {
                if (item == 'rank' || item == 'gender') return true;
                if (!$rootScope.user) return false;
                if ($rootScope.user.usertype.type_name == 'DC' && (item == 'range' || item == 'unit')) {
                    return false;
                }
                else if ($rootScope.user.usertype.type_name == 'RC' && item == 'range') {
                    return false;
                }
                else if ($rootScope.user.usertype.type_name == 'Checker' || $rootScope.user.usertype.type_name == 'Dataentry') {
                    return false;
                }
                else {
                    return true;
                }
            }

            $scope.loadRange = function () {

                if (!$scope.show('range')) return;
                $scope.loading.range = true;
                httpService.range().then(function (data) {
                    $scope.loading.range = false;
                    if (data.status != undefined) {
                        $scope.errorKey = {range: 'range'};
                        $scope.errorMessage = {range: data.statusText};
                        return;
                    }
                    $scope.ranges = data;
                })
                $scope.rangeLoad({param: $scope.selected});
            }
            $scope.loadUnit = function (id) {

                if (!$scope.show('unit')) return;
                $scope.units = $scope.thanas = $scope.kpis = []
                $scope.loading.unit = true;
                httpService.unit(id).then(function (data) {
                    if (data.status != undefined) {
                        $scope.errorKey = {unit: 'unit'};
                        $scope.errorMessage = {unit: data.statusText};
                        return;
                    }
                    $scope.units = data;
                    $scope.loading.unit = false;
                })
                $scope.unitLoad({param: $scope.selected});
            }
            $scope.loadThana = function (d, id) {
                if (!$scope.show('thana')) return;
                $scope.thanas = $scope.kpis = []
                $scope.loading.thana = true;
                httpService.thana(d, id).then(function (data) {
                    // alert(data)
                    $scope.loading.thana = false;
                    if (data.status != undefined) {
                        $scope.errorKey = {thana: 'thana'};
                        $scope.errorMessage = {thana: data.statusText};
                        return;
                    }
                    $scope.thanas = data;
                })
                $scope.thanaLoad({param: $scope.selected});
            }
            $scope.loadKPI = function (d, u, id) {

                //$scope.kpiChange({param:$scope.selected});
                if (!$scope.show('kpi')) return;
                $scope.loading.kpi = true;

                httpService.kpi(d, u, id, $scope.kpiType).then(function (data) {
                    $scope.loading.kpi = false;
                    if (data.status != undefined) {
                        $scope.errorKey = {kpi: 'kpi'};
                        $scope.errorMessage = {kpi: data.statusText};
                        return;
                    }
                    $scope.kpis = data;


                })
                $scope.kpiLoad({param: $scope.selected});
            }
            $scope.loadRank = function () {

                //$scope.kpiChange({param:$scope.selected});
                if (!$scope.show('rank')) return;
                httpService.rank().then(function (data) {
                    if (data.status != undefined) {
                        $scope.errorKey = {rank: 'rank'};
                        $scope.errorMessage = {rank: data.statusText};
                        return;
                    }
                    $scope.ranks = data;

                })
            }
            $scope.changeRange = function (division_id) {
                console.log(division_id)
                if ($scope.type == 'all') {
                    $scope.loadUnit(division_id)
                    $scope.loadThana(division_id)
                    //$scope.loadKPI(division_id)
                }
                else {
                    $scope.loadUnit(division_id)
                }
            }
            $scope.changeUnit = function (d, unit_id) {
                console.log($scope.reset)
                if ($scope.type == 'all') {
                    $scope.loadThana(d, unit_id)
                    //$scope.loadKPI(d,unit_id)
                }
                else {
                    $scope.loadThana(undefined, unit_id)
                }
            }
            $scope.changeThana = function (d, u, thana_id) {
                if ($scope.type == 'all') {
                    $scope.loadKPI(d, u, thana_id)
                }
                else {
                    $scope.loadKPI(undefined, undefined, thana_id)
                }
            }
            if ($scope.showItem.indexOf('rank') > -1) $scope.loadRank();
            $rootScope.$watch('user', function (n, o) {
                if (!n) return;
                if ($rootScope.user.usertype.type_name == 'DC') {
                    $scope.selected.range = $rootScope.user.district.division_id
                    $scope.selected.unit = $rootScope.user.district.id
                    $scope.loadThana(undefined, $rootScope.user.district.id)
                }
                else if ($rootScope.user.usertype.type_name == 'RC') {
                    $scope.selected.range = $rootScope.user.division.id
                    $scope.loadUnit($rootScope.user.division.id)
                    if ($scope.type == 'all') {
                        $scope.loadThana('all');
                        //$scope.loadKPI('all');
                    }
                }
                else if ($rootScope.user.usertype.type_name == 'Super Admin' || $rootScope.user.usertype.type_name == 'Admin' || $rootScope.user.usertype.type_name == 'DG') {
                    if ($scope.type == 'all') {
                        $scope.loadRange();
                        $scope.loadUnit('all');
                        $scope.loadThana('all');
                        //$scope.loadKPI('all');
                    }
                    else {
                        switch ($scope.startLoad) {
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
                }
                $scope.finish = true;
            })

            $scope.$watch('reset.range', function (n, o) {
                if (n) {
                    if ($scope.startLoad != 'range') $scope.ranges = [];
                    $scope.selected.range = $scope.type == 'all' ? 'all' : '';
                }
            })
            $scope.$watch('reset.unit', function (n, o) {
                //alert(1)
                if (n) {
                    if ($scope.startLoad != 'unit' && $rootScope.user.usertype.type_name != 'RC') $scope.units = [];

                    $scope.selected.unit = $scope.data.unit = $scope.type == 'all' ? 'all' : '';
                }
            })
            $scope.$watch('reset.thana', function (n, o) {
                if (n) {
                    if ($scope.startLoad != 'thana' && $rootScope.user.usertype.type_name != 'DC') $scope.thanas = [];
                    $scope.selected.thana = $scope.data.thana = $scope.type == 'all' ? 'all' : '';
                }
            })
            $scope.$watch('reset.kpi', function (n, o) {
                if (n) {
                    if ($scope.startLoad != 'kpi') $scope.kpis = [];
                    $scope.selected.kpi = $scope.data.kpi = $scope.type == 'all' ? 'all' : '';
                }
            })
            $scope.$watch('loadWatch', function (n, o) {
                //alert(n)
                if (n != undefined) {
                    //alert(1)
                    if ($scope.watchChange == 'thana') {
                        $scope.loadThana(undefined, n)
                    }
                }
            })

        },
        templateUrl: '/' + prefix + 'HRM/template_list/range_unit_thana_kpi_rank_gender_template',
        link: function (scope, element, attrs) {
            //alert("aise")
            $rootScope.loadingView = false;
            scope.data = scope.selected;
            $timeout(function () {
                scope.$watch('finish', function (n, o) {
                    if (n) scope.onLoad({param: scope.selected});
                })

            })

            $(element).on('change', "#range", function () {
                //alert('aSsas')
                scope.selected.unit = scope.type == 'all' ? 'all' : ''
                scope.selected.thana = scope.type == 'all' ? 'all' : ''
                scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                scope.rangeChange({param: scope.selected})
            })
            $(element).on('change', '#unit', function () {
                scope.getUnitName= $.trim($(this).children('option:selected').text())
                scope.selected.thana = scope.type == 'all' ? 'all' : ''
                scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                scope.unitChange({param: scope.selected})
            })
            $(element).on('change', "#thana", function () {
                scope.getThanaName = $.trim($(this).children('option:selected').text())
                scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                scope.thanaChange({param: scope.selected})
            })
            $(element).on('change', "#rank", function () {
                scope.rankChange({param: scope.selected})
            })
            $(element).on('change', "#kpi", function () {
                scope.getKpiName = $.trim($(this).children('option:selected').text())
                $timeout(function () {
                    scope.$apply();
                    scope.kpiChange({param: scope.selected})
                })

            })
            $(element).on('change', "#gender", function () {
                scope.genderChange({param: scope.selected})
            })
            $(element).on('change', "#custom", function () {
                scope.customModel = scope.selected.custom;
                $timeout(function () {
                    scope.$apply();
                    scope.customChange({param: scope.selected})
                })

            })
        }
    }
})
GlobalApp.directive('tableSearch', function () {
    return {
        restrict: 'ACE',
        template: '<div class="row" style="margin: 0">' +
        '<div class="col-sm-8" style="padding-left: 0">' +
        '<h5 class="text text-bold" style="color:black;font-size:1.1em">Total : [[results==undefined?0:results.length]]</h5>' +
        '</div> <div class="col-sm-4" style="padding-right: 0">' +
        '<input type="text" class="form-control" ng-model="q" placeholder="[[placeHolder?placeHolder:\'Search Ansar in this table\']]">' +
        '</div>' +
        '</div>',
        scope: {
            q: '=',
            results: '=',
            placeHolder: '@'
        }
    }
})
GlobalApp.directive('databaseSearch', function () {
    return {
        restrict: 'ACE',
        template: '<input type="text" ng-model="q" class="form-control" style="margin-bottom: 10px" ng-change="queue.push(1)" placeholder="[[placeHolder?placeHolder:\'Search by Ansar ID\']]">',
        scope: {
            queue: '=',
            q: '=',
            placeHolder: '@',
            onChange: '&'
        },
        controller: function ($scope) {
            $scope.$watch('queue', function (n, o) {
                //alert(n)
                if (n.length === 1) {
                    $scope.onChange();
                }

            }, true)
        }
    }
})
GlobalApp.directive('formSubmit', function (notificationService, $timeout) {
    return {
        restrict: 'ACE',
        scope: {
            errors: '=?',
            loading: '=?',
            status: '=?',
            confirmBox: '@',
            message: '@',
            onReset: '&',
            resetExcept: '@',
            responseData:'=?'
        },
        link: function (scope, element, attrs) {
            $(element).on('submit', function (e) {
                e.preventDefault();
                if (scope.confirmBox) {
                    $(element).confirmDialog({
                        message: scope.message,
                        ok_button_text: 'Confirm',
                        cancel_button_text: 'Cancel',
                        event: 'submit',
                        ok_callback: function (element) {
                            submitForm()
                        },
                        cancel_callback: function (element) {
                        }
                    })
                }
                else {
                    submitForm();
                }
            })

            function submitForm() {

                $(element).ajaxSubmit({
                    beforeSubmit: function (data) {
                        scope.loading = true;
                        scope.status = false;
                        scope.errors = '';
                        $timeout(function () {
                            scope.$apply();
                        })
                    },
                    success: function (result) {
                        scope.loading = false;
                        // console.log(result)
                        var response = ''
                        try {
                            response = JSON.parse(result);
                        } catch (err) {
                            response = result
                        }
                        if (response.status === true) {
                            notificationService.notify('success', response.message);
                            scope.status = true;
                            $(element).resetForm();
                            scope.responseData = response;
                            scope.onReset();
                        }
                        else if (response.status === false) {
                            scope.status = false;
                            notificationService.notify('error', response.message);
                        }
                        else {
                            scope.status = false;
                            scope.errors = response;
                            console.log(scope.errors)
                        }
                        $timeout(function () {
                            scope.$apply();
                        })
                    },
                    error: function (response) {
                        scope.loading = false;
                        if (response.status == 401) {
                            notificationService.notify('error', "You are not authorize to perform this action");

                        }
                        else notificationService.notify('error', "An unknown error occur. Error code: " + response.status);
                        $timeout(function () {
                            scope.$apply();
                        })
                    }
                })
            }
        }
    }
})
GlobalApp.directive('numericField', function () {
    return {
        restrict: 'A',
        link: function (scope, elem, attr) {

            $(elem).on('keypress', function (e) {
                var key = e.keyCode || e.which;
                if ((key >= 48 && key <= 57) || key == 8 || key == 46 || (key >= 37 && key <= 40)) {
                    return true
                }
                else return false;
            })
        }
    }
})
GlobalApp.controller('jobCircularConstraintController',function ($scope,$filter,$http) {

    $scope.constraint = {
        gender:{male:'',female:''},
        age:{min:'0',max:'0',date:''},
        height:{male:{feet:'0',inch:'0'},female:{feet:'0',inch:'0'}},
        weight:{male:'0',female:'0'},
        chest:{male:'0',female:'0'},
        education:{min:'0',max:'0'}

    };
    $scope.minEduList = {};
    $scope.maxEduList = {};
    $scope.onSave = function (elem) {
        document.getElementsByName(elem)[0].value = JSON.stringify($scope.constraint);


    }
    $http.get('/' + prefix + 'recruitment/educations').then(
        function (response) {
            $scope.minEduList = response.data;
            $scope.maxEduList = response.data;
        },
        function (error) {

        }
    )
    $scope.initConstraint = function (data) {

        $scope.constraint = JSON.parse(data);
        $scope.onSave('constraint')

    }
    $scope.$watch('constraint',function (newVal) {

        $scope.constraint.age.min = $filter('num')($scope.constraint.age.min+"",0);
        $scope.constraint.age.max = $filter('num')($scope.constraint.age.max+"",0);
        $scope.constraint.height.male.feet = $filter('num')($scope.constraint.height.male.feet+"",0);
        $scope.constraint.height.male.inch = $filter('num')($scope.constraint.height.male.inch+"",0);
        $scope.constraint.height.female.feet = $filter('num')($scope.constraint.height.female.feet+"",0);
        $scope.constraint.height.female.inch = $filter('num')($scope.constraint.height.female.inch+"",0);
        $scope.constraint.weight.male = $filter('num')($scope.constraint.weight.male+"",0);
        $scope.constraint.weight.female = $filter('num')($scope.constraint.weight.female+"",0);
        $scope.constraint.chest.male = $filter('num')($scope.constraint.chest.male+"",0);
        $scope.constraint.chest.female = $filter('num')($scope.constraint.chest.female+"",0);

    },true)

})
/*GlobalApp.directive('dataTable',function () {

    return {
        restrict:'A',
        scope:{
            tableTitle:'@',
            headers:'@',
            dataKey:'@',
            itemPerPage:'@',
            onPageChange:'&',
            showItemPerPage:'@',
            requestDetail:'=',
            enableSearch:'@'
        },
        controller:function ($scope, $http) {
            $scope.allLoading = true;
            $scope.q = '';
            $scope.loadPagination = function () {
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i] = false;
                }
            }
            $scope.loadPage = function (page, $event) {
                if ($event !== undefined)  $event.preventDefault();
                if($scope.requestDetail.method==='get'){
                    if($scope.enableSearch) $scope.requestDetail.params['q'] = $scope.q;
                    $scope.requestDetail.params['offset'] = page === undefined ? 0 : page.offset;
                    $scope.requestDetail.params['limit'] = page === undefined ? $scope.itemPerPage : page.limit;
                }
                else{
                    if($scope.enableSearch) $scope.requestDetail.data['q'] = $scope.q;
                    $scope.requestDetail.data['offset'] = page === undefined ? 0 : page.offset;
                    $scope.requestDetail.data['limit'] = page === undefined ? $scope.itemPerPage : page.limit;
                }
                $scope.currentPage = page;
                $scope.loadingPage[$scope.currentPage] = true;
                $http($scope.requestDetail).then(function (response) {
                    $scope.datas = response.data;
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.loadingPage[$scope.currentPage] = false;

                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            }

            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
        }
    }

})*/

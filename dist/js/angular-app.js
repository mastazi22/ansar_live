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

    $rootScope.ws = '';
    $rootScope.user = ''
    $http.get('/' + prefix + 'user_data').then(function (response) {
        $rootScope.user = response.data;
        /* $rootScope.ws = openSocketConnection();
         var p = setInterval(function () {
         console.log($rootScope.ws)
         if($rootScope.ws.readyState===3) clearInterval(p)
         if($rootScope.ws.readyState===1&&$rootScope.ws.bufferedAmount===0){
         $rootScope.ws.send(JSON.stringify({type:'init',data:{'user_id': $rootScope.user.id}}))
         clearInterval(p)
         }
         },500)*/
    })
    $rootScope.loadingView = false;
    $rootScope.dateConvert = function (date) {
        return (moment(date).locale('bn').format('DD-MMMM-YYYY'));
    }
    /*window.onbeforeunload = function (e) {
     $rootScope.ws.onclose = function () {}; // disable onclose handler first
     $rootScope.ws.close();
     }*/

    function openSocketConnection() {
        var ws = new WebSocket("ws://" + window.location.hostname + ":8090/");
        ws.onopen = function (event) {
            console.log(event)

        }
        ws.onerror = function (event) {
            console.log(event)
        }
        ws.onmessage = function (event) {
            console.log(event)
            noty({
                text: event.data,
                layout: 'bottomRight',
                type: 'success'
            })
        }
        ws.onclose = function (event) {
            console.log(event)

        }
        return ws;

    }
});
GlobalApp.filter('num', function () {
    return function (input, defaultValue) {
        var d = parseInt(input === undefined ? '' : input.replace(',', ''));
        return isNaN(d) ? defaultValue == undefined ? '' : defaultValue : d;
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
                event: scope.event || 'click',
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
            var format = attrs.dateFormat || 'dd-M-yy';
            console.log(data)
            if (data) {
                $(element).val(eval(data));

            }

            $(element).datepicker({
                dateFormat: format
            })

        }
    }
})
GlobalApp.directive('typedDatePicker', function () {
    return {
        restrict: 'AC',
        link: function (scope, element, attrs) {
            //alert(scope.event)
            var data = attrs.datePicker
            var format = "DD-MMM-YYYY";
            switch (attrs.calenderType) {
                case 'month':
                    format = "MMMM, YYYY"
                    break;
                case 'year':
                    format = "YYYY"
                    break;
            }

            $(element).datePicker({
                dateFormat: format,
                calenderType: attrs.calenderType
            })

        }
    }
})
GlobalApp.directive('multiDatePicker', function () {
    return {
        restrict: 'AC',
        scope: {
            disabledDates: '=',
            selectedDates: '=',
            month: '@',
            year: '@',
            typee: '@',
            disableElem: '@'
        },
        link: function (scope, element, attrs) {

            var minDate = new Date(scope.year, parseInt(scope.month) - 1, 1);
            var maxDay = (new Date(scope.year, parseInt(scope.month), 0)).getDate();
            var maxDate = new Date(scope.year, parseInt(scope.month) - 1, maxDay);
            $(element).multiDatesPicker({
                dateFormat: 'yy-mm-dd',
                minDate: minDate,
                maxDate: maxDate,
                addDisabledDates: scope.disabledDates,
                onSelect: function (dateText) {
                    if (scope.selectedDates.indexOf(dateText) < 0) {
                        // console.log(scope.selectedDates)
                        $(scope.disableElem).multiDatesPicker("addDates", dateText, "disabled")
                        scope.selectedDates.push(dateText)
                        scope.disabledDates.push(dateText)
                    } else {
                        $(scope.disableElem).multiDatesPicker("removeDates", dateText, "disabled")
                        var i = scope.selectedDates.indexOf(dateText)
                        scope.selectedDates.splice(i, 1)
                        i = scope.disabledDates.indexOf(dateText)
                        scope.disabledDates.splice(i, 1)
                    }
                    console.log(scope.disabledDates);
                    scope.$apply();

                }
            })

        }
    }
})
GlobalApp.directive('datePickerBig', function () {
    return {
        restrict: 'AC',
        link: function (scope, element, attrs) {
            //alert(scope.event)
            var data = attrs.datePickerBig
            var format = attrs.dateFormat || 'dd-M-yy';
            console.log(data)
            // console.log(data)
            $(element).datepicker({
                dateFormat: format,
                changeMonth: true,
                changeYear: true,
                yearRange: "-90:+00"
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
        union: function (division, unit, thana) {
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
                url: '/' + prefix + 'HRM/union/showall',
                params: {unit_id: unit, division_id: division, thana_id: thana}
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
        shortKpi: function (d, u, id, type) {
            return $http({
                url: '/' + prefix + "AVURP/kpi/kpi_name",
                method: 'get',
                params: {division: d, unit: u, thana: id, type: type}
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
        mainTraining: function () {
            return $http({
                url: '/' + prefix + 'HRM/main_training/all',
                method: 'get'
            }).then(function (response) {
                return response.data;
            }, function (response) {
                return response
            })
        },
        subTraining: function (id) {
            return $http({
                url: '/' + prefix + 'HRM/sub_training/all/' + id,
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
                url: '/' + prefix + 'recruitment/category',
                method: 'get',
                params: data
            })
        },
        circular: function (data) {
            return $http({
                url: '/' + prefix + 'recruitment/circular',
                method: 'get',
                params: data
            })
        },
        circularSummery: function (data) {
            return $http({
                url: '/' + prefix + 'recruitment/applicant',
                method: 'post',
                data: data
            })
        },
        searchApplicant: function (url, data) {
            return $http({
                url: url === undefined ? '/' + prefix + 'recruitment/applicant/search' : url,
                method: 'post',
                data: data
            })
        },
        applicantQuota: function (data) {
            return $http({
                url: '/' + prefix + 'recruitment/settings/applicant_quota',
                method: 'post',
                data: data
            })
        },
    }
})
GlobalApp.factory('notificationService', function () {
    return {
        notify: function (type, message, time) {
            //$.noty.closeAll();
            noty({
                type: type,
                text: message,
                layout: 'top',
                maxVisible: 5,
                timeout: time ? time : 5000,
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
            showItem: '@',// ['range','unit','thana','kpi','short_kpi']
            rangeChange: '&',//{func()}
            unitChange: '&',//{func()}
            thanaChange: '&',//{func()}
            kpiChange: '&',//{func()}
            shortKpiChange: '&',//{func()}
            rankChange: '&',//{func()}
            genderChange: '&',//{func()}
            rangeLoad: '&',//{func()}
            unitLoad: '&',//{func()}
            thanaLoad: '&',//{func()}
            kpiLoad: '&',//{func()}
            shortKpiLoad: '&',//{func()}
            kpiDisabled: '=?',
            shortKpiDisabled: '=?',
            rangeDisabled: '=?',
            thanaDisabled: '=?',
            unitDisabled: '=?',
            kpiFieldDisabled: '=?',
            shortKpiFieldDisabled: '=?',
            unitFieldDisabled: '=?',
            rangeFieldDisabled: '=?',
            thanaFieldDisabled: '=?',
            unionFieldDisabled: '=?',
            loadWatch: '=?',
            watchChange: '@',
            onLoad: "&",
            type: '@',//['all','single']
            startLoad: '@',//['range','unit','thana','kpi']
            fieldWidth: '=?',
            fieldName: '=?',
            layoutVertical: '@',
            getKpiName: '=?',
            getShortKpiName: '=?',
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
            resetAll: '@',
            callFunc: '=?'
        },
        controller: function ($scope, $rootScope, httpService) {
            $scope.items = JSON.parse($scope.showItem.replace(/\\n/g, "\\n")
                .replace(/\'/g, "\""));
            // console.log($scope.showItem);
            $scope.selected = {
                range: $scope.type == 'all' ? 'all' : '',
                unit: $scope.type == 'all' ? 'all' : '',
                thana: $scope.type == 'all' ? 'all' : '',
                union: $scope.type == 'all' ? 'all' : '',
                kpi: $scope.type == 'all' ? 'all' : '',
                shortKpi: $scope.type == 'all' ? 'all' : '',
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
                        union: $scope.type == 'all' ? 'all' : '',
                        kpi: $scope.type == 'all' ? 'all' : '',
                        shortKpi: $scope.type == 'all' ? 'all' : '',
                        rank: $scope.type == 'all' ? 'all' : '',
                        gender: $scope.type == 'all' ? 'all' : '',
                        custom: $scope.customModel
                    }
                    if ($rootScope.user.usertype.type_name === 'DC') {
                        $scope.kpis = [];
                        $scope.shortKpis = [];
                    }
                    else if ($rootScope.user.usertype.type_name === 'RC') {
                        $scope.kpis = [];
                        $scope.shortKpis = [];
                        $scope.thanas = [];
                    }
                    else {
                        $scope.kpis = [];
                        $scope.shortKpis = [];
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

                // console.log($scope.items)
                return $scope.items.indexOf(item) > -1 && hasPermission(item);
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
                $scope.units = $scope.thanas = $scope.kpis = $scope.shortKpis = [];
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
                $scope.thanas = $scope.kpis = $scope.shortKpis = []
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
            $scope.loadUnion = function (d, u, id) {
                if (!$scope.show('union')) return;
                $scope.unions = $scope.kpis = $scope.shortKpis = []
                $scope.loading.union = true;
                httpService.union(d, u, id).then(function (data) {
                    // alert(data)
                    $scope.loading.union = false;
                    if (data.status != undefined) {
                        $scope.errorKey = {union: 'union'};
                        $scope.errorMessage = {union: data.statusText};
                        return;
                    }
                    $scope.unions = data;
                })
                $scope.unionLoad({param: $scope.selected});
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
            $scope.loadShortKPI = function (d, u, id) {

                //$scope.kpiChange({param:$scope.selected});
                if (!$scope.show('short_kpi')) return;
                $scope.loading.shortKpi = true;

                httpService.shortKpi(d, u, id, $scope.kpiType).then(function (data) {
                    $scope.loading.shortKpi = false;
                    if (data.status != undefined) {
                        $scope.errorKey = {kpi: 'kpi'};
                        $scope.errorMessage = {kpi: data.statusText};
                        return;
                    }
                    $scope.shortKpis = data;


                })
                $scope.shortKpiLoad({param: $scope.selected});
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
                    $scope.loadShortKPI(d, u, thana_id)
                    $scope.loadUnion(d, u, thana_id)
                }
                else {
                    $scope.loadUnion(undefined, undefined, thana_id)
                    $scope.loadKPI(undefined, undefined, thana_id)
                    $scope.loadShortKPI(d, u, thana_id)
                }
            }
            if ($scope.showItem.indexOf('rank') > -1) $scope.loadRank();
            $rootScope.$watch('user', function (n, o) {
                var p = window.location.pathname.split('/');
                if (!n) return;
                if ($rootScope.user.usertype.type_name == 'DC') {
                    $scope.selected.range = $rootScope.user.district.division_id
                    if (p.length > 1 && p[1] === 'recruitment' && $rootScope.user.rec_district) $scope.selected.unit = $rootScope.user.rec_district.id
                    else $scope.selected.unit = $rootScope.user.district.id
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
                            case 'short_kpi':
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
            $scope.parseItem = function(){
                // alert(1);
                $scope.showItem = JSON.parse($scope.showItem.replace(/\\n/g, "\\n")
                    .replace(/\'/g, "\""));
                console.log()
            }

        },
        templateUrl: '/' + prefix + 'HRM/template_list/range_unit_thana_kpi_rank_gender_template',
        link: function (scope, element, attrs) {
            $rootScope.loadingView = false;
            scope.data = scope.selected;
            $timeout(function () {
                scope.$watch('finish', function (n, o) {
                    if (n) scope.onLoad({param: scope.selected});
                })

            })
            if (scope.callFunc) {
                scope.callFunc['reset'] = function () {
                    // alert(1);
                    scope.selected.unit = scope.type == 'all' ? 'all' : ''
                    scope.selected.thana = scope.type == 'all' ? 'all' : ''
                    scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                    scope.selected.shortKpi = scope.type == 'all' ? 'all' : ''
                    scope.selected.range = scope.type == 'all' ? 'all' : ''
                    // scope.$digest();
                }
            }
            $(element).on('change', "#range", function () {
                //alert('aSsas')
                scope.selected.unit = scope.type == 'all' ? 'all' : ''
                scope.selected.thana = scope.type == 'all' ? 'all' : ''
                scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                scope.selected.shortKpi = scope.type == 'all' ? 'all' : ''
                scope.rangeChange({param: scope.selected})
            })
            $(element).on('change', '#unit', function () {
                scope.getUnitName = $.trim($(this).children('option:selected').text())
                scope.selected.thana = scope.type == 'all' ? 'all' : ''
                scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                scope.selected.shortKpi = scope.type == 'all' ? 'all' : ''
                scope.unitChange({param: scope.selected})
            })
            $(element).on('change', "#thana", function () {
                scope.getThanaName = $.trim($(this).children('option:selected').text())
                scope.selected.kpi = scope.type == 'all' ? 'all' : ''
                scope.selected.shortKpi = scope.type == 'all' ? 'all' : ''
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
            $(element).on('change', "#short_kpi", function () {
                scope.getShortKpiName = $.trim($(this).children('option:selected').text())
                $timeout(function () {
                    scope.$apply();
                    scope.shortKpiChange({param: scope.selected})
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
            responseData: '=?'
        },
        link: function (scope, element, attrs) {
            $(element).on('submit', function (e) {
                e.preventDefault();
                if (scope.confirmBox) {
                    $(element).confirmDialog({
                        message: scope.message || "Are u sure?",
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
                        else if (response.status == 422) {
                            notificationService.notify('error', "Invalid request");

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
GlobalApp.controller('jobCircularConstraintController', function ($scope, $filter, $http) {

    $scope.constraint = {
        gender: {male: '', female: ''},
        age: {min: '0', max: '0', minDate: '', maxDate: ''},
        height: {male: {feet: '0', inch: '0'}, female: {feet: '0', inch: '0'}},
        weight: {male: '0', female: '0'},
        chest: {male: {min: '0', max: '0'}, female: {min: '0', max: '0'}},
        education: {min: '0', max: '0'}

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

        var d = JSON.parse(data);
        $scope.constraint = d;
        $scope.onSave('constraint')

    }
    $scope.$watch('constraint', function (newVal) {

        $scope.constraint.age.min = $filter('num')($scope.constraint.age.min + "", 0);
        $scope.constraint.age.max = $filter('num')($scope.constraint.age.max + "", 0);
        $scope.constraint.height.male.feet = $filter('num')($scope.constraint.height.male.feet + "", 0);
        $scope.constraint.height.male.inch = $filter('num')($scope.constraint.height.male.inch + "", 0);
        $scope.constraint.height.female.feet = $filter('num')($scope.constraint.height.female.feet + "", 0);
        $scope.constraint.height.female.inch = $filter('num')($scope.constraint.height.female.inch + "", 0);
        $scope.constraint.weight.male = $filter('num')($scope.constraint.weight.male + "", 0);
        $scope.constraint.weight.female = $filter('num')($scope.constraint.weight.female + "", 0);
        $scope.constraint.chest.male.min = $filter('num')($scope.constraint.chest.male.min + "", 0);
        $scope.constraint.chest.male.max = $filter('num')($scope.constraint.chest.male.max + "", 0);
        $scope.constraint.chest.female.min = $filter('num')($scope.constraint.chest.female.min + "", 0);
        $scope.constraint.chest.female.max = $filter('num')($scope.constraint.chest.female.max + "", 0);

    }, true)
    $scope.onChangeQuota = function () {
        if ($scope.quota_type === "" || $scope.quota_type == null) return;
        if ($scope.constraint.age.quota.type.indexOf($scope.quota_type) < 0) {
            $scope.constraint.age.quota.type.push($scope.quota_type);
        }
        $scope.formatValue();
    };
    $scope.formatValue = function () {
        document.getElementById("selected-quota-type").innerHTML = '';
        for (var i = 0; i < $scope.constraint.age.quota.type.length; i++) {
            var data = $scope.constraint.age.quota.type[i].replace(new RegExp("_", 'g'), " ");
            data = data.charAt(0).toUpperCase() + data.slice(1);
            jQuery("#selected-quota-type").append('<div class="selected-quota" data-value =' +
                $scope.constraint.age.quota.type[i] + ' >' + data + '</div>');
        }
    };
    jQuery("#selected-quota-type").on("click", "div.selected-quota", function (event) {
        var data2 = jQuery(this).attr("data-value");
        var index = $scope.constraint.age.quota.type.indexOf(data2);
        if (index > -1) {
            $scope.constraint.age.quota.type.splice(index, 1);
            $scope.formatValue();
        }
    });

})
GlobalApp.directive('paginate', function () {
    return {
        restrict: 'A',
        scope: {
            ref: '&'
        },
        link: function (scope, elem, attr) {
            $(elem).find('.pagination a').on('click', function (e) {
                e.preventDefault();
                var urll = $(this).attr('href')
                scope.ref({url: urll})
            })

        }
    }
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

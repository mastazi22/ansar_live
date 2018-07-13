@extends('template.master')
@section('title','Grant Leave')
@section('breadcrumb')
    {!! Breadcrumbs::render('grant_leave') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("GrantLeave", function ($scope, $http, notificationService) {
            $scope.param = {
                ansar_id:'',
                selectedDates:[],
                leave_type:''
            }
            $scope.searchAnsar = function () {
                $scope.param.selectedDates = [];
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('SD.leave.create')}}',
                    method:'get',
                    params:{ansar_id:$scope.param.ansar_id}
                }).then(function (success) {
                    $scope.allLoading = false;
                    if(success.data.status){
                        $scope.personalDetails = success.data.data;
                        $scope.message = ''
                        $scope.notFound = false
                    } else{
                        $scope.notFound = true
                        $scope.message = success.data.message
                        $scope.personalDetails = null;
                    }
                },function (error) {
                    $scope.allLoading = false;
                })
            }
            $scope.submitLeave = function () {
                $scope.param.ansar_id = $scope.personalDetails.personal_details.ansar_id;
                $scope.param["kpi_id"] = $scope.personalDetails.personal_details.embodiment.kpi.id;
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('SD.leave.store')}}',
                    method:'post',
                    data:$scope.param
                }).then(function (success) {
                    $scope.allLoading = false;
                    if(success.data.status){
                        $scope.personalDetails = null;
                        $scope.param = {
                            ansar_id:'',
                            selectedDates:[],
                            leave_type:''
                        }
                        notificationService.notify("success",success.data.message)
                    } else{
                        notificationService.notify("error",success.data.message)
                    }
                },function (error) {
                    $scope.allLoading = false;
                    notificationService.notify("error","an error occur while submitting. error code:"+error.status)
                })
            }
        })
        GlobalApp.directive("calender", function (notificationService) {
            return {
                restrict: 'AE',
                scope: {
                    showOnlyCurrentYear: '@',
                    showOnlyCurrentMonth: '@',
                    selectedDates: '=?',
                    disabledDates: '=?',
                    monthRange: '@',
                    disableDateSelection:'=?',
                    disableNavigationBeforeMonth:'@'
                },
                controller: function ($scope) {
                    $scope.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    $scope.selectedDates = [];
                    var currentDate = moment();
                    $scope.currentMonth = {
                        totalDays: currentDate.daysInMonth(),
                        month: currentDate.get('month'),
                        year: currentDate.get('year'),
                        date: currentDate.get('date'),
                    };
                    $scope.previousMonth = {
                        totalDays: moment().date(1).month($scope.currentMonth.month - 1).year($scope.currentMonth.year).daysInMonth(),
                        month: $scope.currentMonth.month - 1,
                        year: $scope.currentMonth.year,
                        date: 1,
                    };
                    $scope.nextMonth = {
                        totalDays: moment().date(1).month($scope.currentMonth.month + 1).year($scope.currentMonth.year).daysInMonth(),
                        month: $scope.currentMonth.month + 1,
                        year: $scope.currentMonth.year,
                        date: 1,
                    };
                    $scope.next = function (event) {
                        event.preventDefault();
                        var currentDate = moment().date(1).month($scope.nextMonth.month%12).year($scope.nextMonth.year+Math.floor($scope.nextMonth.month/12));
                        $scope.currentMonth = {
                            totalDays: currentDate.daysInMonth(),
                            month: currentDate.get('month'),
                            year: currentDate.get('year'),
                            date: currentDate.get('date'),
                        };
                        $scope.previousMonth = {
                            totalDays: moment().date(1).month($scope.currentMonth.month - 1).year($scope.currentMonth.year).daysInMonth(),
                            month: $scope.currentMonth.month - 1,
                            year: $scope.currentMonth.year,
                            date: 1,
                        };
                        $scope.nextMonth = {
                            totalDays: moment().date(1).month($scope.currentMonth.month + 1).year($scope.currentMonth.year).daysInMonth(),
                            month: $scope.currentMonth.month + 1,
                            year: $scope.currentMonth.year,
                            date: 1,
                        };
                        $scope.makeCalender();
                    }
                    $scope.previous = function (event) {
                        event.preventDefault();
                        var currentDate = moment().date(1).month($scope.previousMonth.month).year($scope.previousMonth.year);
                        $scope.currentMonth = {
                            totalDays: currentDate.daysInMonth(),
                            month: currentDate.get('month'),
                            year: currentDate.get('year'),
                            date: currentDate.get('date'),
                        };
                        $scope.previousMonth = {
                            totalDays: moment().date(1).month($scope.currentMonth.month - 1).year($scope.currentMonth.year).daysInMonth(),
                            month: $scope.currentMonth.month - 1,
                            year: $scope.currentMonth.year,
                            date: 1,
                        };
                        $scope.nextMonth = {
                            totalDays: moment().date(1).month($scope.currentMonth.month + 1).year($scope.currentMonth.year).daysInMonth(),
                            month: $scope.currentMonth.month + 1,
                            year: $scope.currentMonth.year,
                            date: 1,
                        };
                        $scope.makeCalender();
                    }
                    $scope.makeCalender = function () {
                        console.log($scope.selectedDates)
                        $("body").find(".date").removeClass("selected")
                        $scope.calender = new Array(6);
                        makePreviousCalender();
//                        alert(1)
                        var dd = 1;
                        var nn = 1;
                        var wd = moment().date(1).month($scope.currentMonth.month).year($scope.currentMonth.year).day()
                        for (var i = 0; i < 6; i++) {
//                            if(i*7+1>$scope.currentMonth.totalDays) break;
                            for (var j = 0; j < 7; j++) {
                                if (dd <= $scope.currentMonth.totalDays) {
                                    if (j >= wd) {
                                        $scope.calender[i][wd++] = {
                                            day: dd++,
                                            tag: "cur"
                                        }
                                    }
                                } else {
                                    $scope.calender[i][j] = {
                                        day: nn++,
                                        tag: "next"
                                    };
                                }
                            }
                            wd = 0;
                            if (i + 1 < 6) {
                                $scope.calender[i + 1] = new Array(7);
                            }
                        }

                        console.log($scope.calender)
                    }
                    function makePreviousCalender() {
                        var j = 0;
                        $scope.calender[0] = new Array(7);
                        var lastWeekDay = moment().date($scope.previousMonth.totalDays)
                                .month($scope.previousMonth.month)
                                .year($scope.previousMonth.year).day() % 6;
                        console.log(lastWeekDay)
                        for (var i = $scope.previousMonth.totalDays - lastWeekDay; i <= $scope.previousMonth.totalDays; i++) {
                            $scope.calender[0][j++] = {
                                day: i, tag: "pre"
                            }
                        }
                    }
                    $scope.checkDate = function (d,m,y){
                        var data = {
                            day:+d,
                            month:+m,
                            year:+y,
                        }
                        var t = false;
                        $scope.selectedDates.forEach(function (item) {
                            if(item.day==+d&&item.month==+m&&item.year==+y) {
                                t = true;
                                return;
                            }
                        })
                        console.log(t)
                        return t;
                    }
                    $scope.findIndex = function (d,m,y) {
                        var t = -1;
                        $scope.selectedDates.forEach(function (item,index) {
                            if(item.day==+d&&item.month==+m&&item.year==+y) {
                                t = index;
                                return;
                            }
                        })
                        return t;
                    }
                },
                link: function (scope, elem, attr) {
                    scope.selectedDates = [];
                    scope.makeCalender();

                    var isMouseDown = false;
                    $(elem).on("mousedown",".date-row",function (event) {
                       isMouseDown = true
                    })
                    $(elem).on("click",".date-row>.date",function (event) {
                        if($(this).hasClass("selected")&&$(event.target).attr("data-tag")==="cur"){
                            var index = scope.findIndex($(event.target).attr("data-day"),$(event.target).attr("data-month"),$(event.target).attr("data-year"));
                            scope.selectedDates.splice(index,1);

                        } else if($(event.target).attr("data-tag")==="cur"){
                            if(scope.disableDateSelection){
                                notificationService.notify("error","you can`t select anymore date");
                                return;
                            }
                            var data = {
                                day:+$(event.target).attr("data-day"),
                                month:+$(event.target).attr("data-month"),
                                year:+$(event.target).attr("data-year"),
                            }
                            scope.selectedDates.push(data);
                        }

                        if($(event.target).attr("data-tag")==="cur") {
                            $(this).toggleClass("selected")
                            scope.$apply();
                        }
                    })
                    $(elem).on("mouseup",".date-row",function (event) {
                        isMouseDown = false
                    })
                    $(elem).on("mousemove",".date-row",function (event) {
                        if(isMouseDown){
                            if(scope.disableDateSelection){
                                notificationService.notify("error","you can`t select anymore date");
                                return;
                            }
                            if($(event.target).hasClass("date")&&!$(event.target).hasClass("selected")&&$(event.target).attr("data-tag")==="cur"){
                                $(event.target).addClass("selected")
                                var data = {
                                    day:+$(event.target).attr("data-day"),
                                    month:+$(event.target).attr("data-month"),
                                    year:+$(event.target).attr("data-year"),
                                }
                                scope.selectedDates.push(data);
                                scope.$apply();
                            }
                        }
                    })
                },
                template: `<div class="big-date-picker">
                            <div class="header">
                                <div class="row">
                                    <div ng-class="{'col-sm-8':!showOnlyCurrentMonth,'col-sm-12':showOnlyCurrentMonth}">
                                       <h3 style="margin: 4px">
                                           [[months[currentMonth.month] ]], [[currentMonth.year]]
                                       </h3>
                                    </div>
                                    <div class="col-sm-4" ng-if="!showOnlyCurrentMonth">
                                       <div class="btn-group">
                                           <a href="#" class="btn btn-default" ng-disabled="(previousMonth.month<0||previousMonth.month<disableNavigationBeforeMonth)&&showOnlyCurrentYear" ng-click="previous($event)">
                                               <i class="fa fa-angle-left"></i>
                                           </a>
                                           <a href="#" class="btn btn-default" ng-disabled="nextMonth.month>11&&showOnlyCurrentYear" ng-click="next($event)">
                                               <i class="fa fa-angle-right"></i>
                                           </a>
                                       </div>
                                    </div>
                                </div>
                            </div>
                            <div class="body">
                                <div class="week-row">
                                    <div class="week-title">Sun</div>
                                    <div class="week-title">Mon</div>
                                    <div class="week-title">Tue</div>
                                    <div class="week-title">Wed</div>
                                    <div class="week-title">Thu</div>
                                    <div class="week-title">Fri</div>
                                    <div class="week-title">Sat</div>
                                </div>
                                <div class="date-row" ng-repeat="c in calender track by $index">
                                    <div ng-if="d.tag=='pre'" class="date"  ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur','cursor-disabled':d.tag!='cur','selected':checkDate(d.day,previousMonth.month,previousMonth.year)}"
                                    data-tag="[[d.tag]]" data-day="[[d.day]]" data-month="[[previousMonth.month]]"  data-year="[[previousMonth.year]]"
                                    >[[d.day]]</div>

                                    <div ng-if="d.tag=='cur'" class="date"  ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur','cursor-disabled':d.tag!='cur','selected':checkDate(d.day,currentMonth.month,currentMonth.year)}"
                                    data-tag="[[d.tag]]" data-day="[[d.day]]" data-month="[[currentMonth.month]]"  data-year="[[currentMonth.year]]"
                                    >[[d.day]]</div>

                                    <div ng-if="d.tag=='next'" class="date"  ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur','cursor-disabled':d.tag!='cur','selected':checkDate(d.day,nextMonth.month,nextMonth.year)}"
                                    data-tag="[[d.tag]]" data-day="[[d.day]]" data-month="[[nextMonth.month]]"  data-year="[[nextMonth.year]]"
                                    >[[d.day]]</div>
                                </div>
                            </div>
                        </div>`
            }
        })
    </script>

    <section class="content" ng-controller="GrantLeave">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-header">
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <form>
                            <div class="form-group">
                                <label for="" class="control-label"
                                       style="display: block;text-align: center;font-size: 18px">
                                    Enter Ansar ID To Grant Leaves
                                </label>
                                <div class="input-group input-group-lg">

                                    <input type="text" class="form-control " ng-model="param.ansar_id" placeholder="Ansar ID">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" ng-click="searchAnsar()">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="col-sm-6 col-centered" ng-if="personalDetails">
                        <div class="personal-details">
                            <p>Name: [[personalDetails.personal_details.ansar_name_bng]]</p>
                            <p>Rank: [[personalDetails.personal_details.designation.name_bng]]</p>
                            <p>KPI Name: [[personalDetails.personal_details.embodiment.kpi.kpi_name]]</p>
                            <p>Total Days Left: [[personalDetails.total_leave-param.selectedDates.length]]</p>
                        </div>
                        <calender disable-date-selection="personalDetails.total_leave-param.selectedDates.length<=0" selected-dates="param.selectedDates" show-only-current-year="true" disable-navigation-before-month="6"></calender>
                        <div class="form-group" style="margin-top: 10px;">
                            <label class="control-label">
                                <div class="styled-checkbox">
                                    <input ng-model="param.leave_type" id="exclude_weekend" name="exclude_weekend" type="checkbox" ng-true-value="'holiday'" ng-false-value="'regular'">
                                    <label for="exclude_weekend"></label>
                                </div>
                                Exclude weekend from leave count
                            </label>
                        </div>
                        <div class="form-group" ng-click="submitLeave()">
                            <button class="btn btn-primary pull-right btn-md">
                                <i class="fa fa-check"></i>&nbsp;Grant Leave
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-6 col-centered" ng-if="notFound">
                        <p class="text text-danger text-center">[[message]]</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .personal-details{
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .personal-details>p{
            margin-bottom: 0;
        }
        .cursor-pointer {
            cursor: pointer;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .cursor-disabled {
            cursor: not-allowed;
            color: #cccccc;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .big-date-picker {
            display: block;
            border: 1px solid #cccccc;;
            /*padding: 10px;*/
        }

        .big-date-picker > .header {
            text-align: center;
            padding: 5px 10px;
            font-size: 16px;
            font-weight: bold;
            overflow: hidden;
            border-bottom: 1px solid #cccccc;
            /*height: 50px;*/
        }

        .big-date-picker > .header span {
            display: inline-block;
            vertical-align: middle;
        }

        .big-date-picker > .body > .date-row, .big-date-picker > .body > .week-row {
            display: flex;
        }

        .big-date-picker > .body > .date-row {
            border-top: 1px solid #cccccc;
            border-bottom: 1px solid #cccccc;
        }

        .big-date-picker > .body > .date-row > .date {
            flex: 1;
            height: 50px;
            align-items: center;
            justify-content: center;
            display: flex;
            font-weight: bold;
            border-right: 1px solid #cccccc;
        }

        .date-row > .date:not(:first-child) {
            /*border-left: none !important;*/
            border-left: 1px solid #cccccc;
            border-right: none !important;
        }

        .date-row > .date:first-child {
            border-right: none !important;
        }

        .date-row:not(:nth-child(2)) {
            border-top: none !important;
        }

        .date-row:last-child {
            border-bottom: none !important;
        }

        .week-row > .week-title {
            flex: 1;
            font-weight: bold;
            text-align: center;
            padding: 5px 10px;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .date-row > .cursor-pointer.selected {
            background: #77a9c2;
            color: #ffffff;
        }
        .date-row > .cursor-disabled.selected {
            background: rgba(119, 169, 194, 0.45);
            color: #ffffff;
        }

    </style>
    <script>
    </script>
@endsection
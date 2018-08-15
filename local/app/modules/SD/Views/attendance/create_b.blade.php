@extends('template.master')
@section('title','Take Attendance')
@section('breadcrumb')
    {!! Breadcrumbs::render('grant_leave') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("GrantLeave", function ($scope, $http, notificationService) {
            $scope.param = {}
            $scope.searchAnsar = function () {
                $scope.param.selectedDates = [];
                $scope.param.disabledDates = [];
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('SD.attendance.load_datab')}}',
                    method:'post',
                    data:{ansar_id:$scope.param.ansar_id}
                }).then(function (success) {
                    $scope.allLoading = false;
                    $scope.personalDetails = success.data.personalDetails;
                    $scope.attData = success.data.data;
                    for(var i=0;i<$scope.attData.length;i++){
                        $scope.param.selectedDates[i] = {
                            kpi_id:$scope.attData[i].kpi_id,
                            present:[],
                            absent:[],
                            leave:[],
                        }
                        $scope.param.disabledDates[i] = {
                            present:$scope.param.selectedDates[i].leave.concat($scope.param.selectedDates[i].absent),
                            absent:$scope.param.selectedDates[i].present.concat($scope.param.selectedDates[i].leave),
                            leave:$scope.param.selectedDates[i].present.concat($scope.param.selectedDates[i].absent),
                        }
                    }
                    $scope.message = ''
                    $scope.notFound = success.data.personalDetails?false:true;
                },function (error) {
                    $scope.allLoading = false;
                })
            }
            $scope.$watch('param.selectedDates',function (n,o) {
                if($scope.attData===undefined) return;
                for(var i=0;i<$scope.attData.length;i++){
                    $scope.param.disabledDates[i] = {
                        present:$scope.param.selectedDates[i].leave.concat($scope.param.selectedDates[i].absent),
                        absent:$scope.param.selectedDates[i].present.concat($scope.param.selectedDates[i].leave),
                        leave:$scope.param.selectedDates[i].present.concat($scope.param.selectedDates[i].absent),
                    }
                }
            },true)
            $scope.submitData = function () {
                $scope.param.ansar_id = $scope.personalDetails.ansar_id;
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('SD.attendance.storeb')}}',
                    method:'post',
                    data:$scope.param
                }).then(function (success) {
                    $scope.allLoading = false;
                    if(success.data.status){
                        $scope.personalDetails = null;
                        $scope.param = {}
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
                    showOnlyMonth: '@',
                    selectedDates: '=?',
                    disabledDates: '=?',
                    enabledDates: '=?',
                    monthRange: '@',
                    disableDateSelection:'=?',
                    disableNavigationBeforeMonth:'@',
                    disableDateBeforeCurrentDate:'@'
                },
                controller: function ($scope) {
                    $scope.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    $scope.selectedDates = [];
                    var currentDate = moment();
                    $scope.current = {
                        month: currentDate.get('month'),
                        year: currentDate.get('year'),
                        date: currentDate.get('date'),
                    };
                    $scope.currentMonth = {
                        totalDays: currentDate.daysInMonth(),
                        month: $scope.showOnlyMonth?$scope.showOnlyMonth:currentDate.get('month'),
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
//                        console.log($scope.selectedDates)
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

//                        console.log($scope.calender)
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
//                        console.log(t)
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
                    $scope.isEnabled = function (d,m,y) {
                        var t = -1;
                        if($scope.enabledDates==undefined) return true;

                        $scope.enabledDates.forEach(function (item,index) {
                            if(item.day==+d&&item.month==+m&&item.year==+y) {
                                t = index;
                                return;
                            }
                        })
                        return t>=0;
                    }
                    $scope.findDisabledIndex = function (d,m,y) {
                        if($scope.disabledDates!=undefined&&$scope.disabledDates.length>0) {
//                            console.log($scope.disabledDates)
//                            console.log(d+" "+m+" "+y)
                        }
                        var t = -1;
                        if($scope.disabledDates==undefined) return -1
                        $scope.disabledDates.forEach(function (item,index) {
                            if(item.day==+d&&item.month==+m&&item.year==+y) {
                                t = index;
                                return;
                            }
                        })
                        return t;
                    }
                    $scope.disableDate = function (d,m,y) {
//                        console.log(d)
                        return (+$scope.current.date>+d&&+$scope.current.month==+$scope.currentMonth.month&&+$scope.current.year==+$scope.currentMonth.year&&$scope.disableDateBeforeCurrentDate)||($scope.findDisabledIndex(d,m,y)>=0);
                    }
                },
                link: function (scope, elem, attr) {
                    scope.selectedDates = [];
                    scope.makeCalender();

                    var isMouseDown = false;
                    var moveCount = 0;
                    $(elem).on("mousedown",".date-row",function (event) {
                       isMouseDown = true
                        console.log("down")
                    })
                    $(elem).on("click",".date-row>.date:not(.cursor-disabled)",function (event) {
                        event.stopPropagation();
                        console.log("click")
                        isMouseDown = false;
                        /*if(isMouseMove){
                            isMouseMove = false;
                            return;
                        }*/
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
                    $(elem).on("mouseup",function (event) {
                        isMouseDown = false
                        console.log("up")
                        moveCount = 0;
                    })
                    $(elem).on("mousemove",".date-row",function (event) {
                        console.log("isMouseDown : "+isMouseDown)
                        if(isMouseDown&&moveCount++>0){
//                            alert(2)
                            isMouseMove = true;
                            if(scope.disableDateSelection){
                                notificationService.notify("error","you can`t select anymore date");
                                return;
                            }
                            if($(event.target).hasClass("date")&&!$(event.target).hasClass("selected")&&!$(event.target).hasClass("cursor-disabled")&&$(event.target).attr("data-tag")==="cur"){
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
                                    <div ng-class="{'col-sm-8':!showOnlyCurrentMonth&&!showOnlyMonth,'col-sm-12':showOnlyCurrentMonth&&showOnlyMonth}">
                                       <h3 style="margin: 4px">
                                           [[months[currentMonth.month] ]], [[currentMonth.year]]
                                       </h3>
                                    </div>
                                    <div class="col-sm-4" ng-if="!showOnlyCurrentMonth&&!showOnlyMonth">
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
                                    <div ng-if="d.tag=='pre'" class="date"  ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur'&&isEnabled(d.day,currentMonth.month,currentMonth.year),'cursor-disabled':d.tag!='cur'||disableDate(d.day,previousMonth.month,previousMonth.year)||!isEnabled(d.day,currentMonth.month,currentMonth.year),'selected':checkDate(d.day,previousMonth.month,previousMonth.year)}"
                                    data-tag="[[d.tag]]" data-day="[[d.day]]" data-month="[[previousMonth.month]]"  data-year="[[previousMonth.year]]"
                                    >[[d.day]]</div>

                                    <div ng-if="d.tag=='cur'" class="date"  ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur'&&!disableDate(d.day,currentMonth.month,currentMonth.year)&&isEnabled(d.day,currentMonth.month,currentMonth.year),'cursor-disabled':d.tag!='cur'||!isEnabled(d.day,currentMonth.month,currentMonth.year)||disableDate(d.day,currentMonth.month,currentMonth.year),'selected':checkDate(d.day,currentMonth.month,currentMonth.year)}"
                                    data-tag="[[d.tag]]" data-day="[[d.day]]" data-month="[[currentMonth.month]]"  data-year="[[currentMonth.year]]"
                                    >[[d.day]]</div>

                                    <div ng-if="d.tag=='next'" class="date"  ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur'&&isEnabled(d.day,currentMonth.month,currentMonth.year),'cursor-disabled':d.tag!='cur'||disableDate(d.day,nextMonth.month,nextMonth.year)||!isEnabled(d.day,currentMonth.month,currentMonth.year),'selected':checkDate(d.day,nextMonth.month,nextMonth.year)}"
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
                                    Enter Ansar ID For Take Attandance
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
                            <p>Name: [[personalDetails.ansar_name_bng]]</p>
                            <p>Rank: [[personalDetails.designation.name_bng]]</p>
                        </div>

                    </div>
                    <div class="col-sm-6 col-centered" ng-if="notFound">
                        <p class="text text-danger text-center">This ansar does not embodied in July,2018 or previous month</p>
                    </div>
                </div>
                <div class="container-fluid" ng-if="personalDetails">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default" ng-repeat="att in attData">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#kpi_[[att.kpi_id]]">
                                        [[att.kpi_name]]</a>
                                </h4>
                            </div>
                            <div id="kpi_[[att.kpi_id]]" class="panel-collapse collapse" ng-class="{'in':$index==0}">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h3 class="text-center">Select date for present</h3>
                                            <calender enabled-dates="att.dates" disabled-dates="param.disabledDates[$index].present"  selected-dates="param.selectedDates[$index].present" show-only-current-year="true" show-only-month="6"></calender>
                                        </div>
                                        <div class="col-sm-4">
                                            <h3 class="text-center">Select date for absent</h3>
                                            <calender enabled-dates="att.dates"  disabled-dates="param.disabledDates[$index].absent"  selected-dates="param.selectedDates[$index].absent" show-only-current-year="true" show-only-month="6"></calender>
                                        </div>
                                        <div class="col-sm-4">
                                            <h3 class="text-center">Select date for leave</h3>
                                            <calender enabled-dates="att.dates" disabled-dates="param.disabledDates[$index].leave"  selected-dates="param.selectedDates[$index].leave" show-only-current-year="true" show-only-month="6"></calender>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary pull-right" ng-click="submitData()">
                            Submit Attandance
                        </button>
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
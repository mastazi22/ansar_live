@extends('template.master')
@section('title','Grant Leave')
@section('breadcrumb')
    {!! Breadcrumbs::render('grant_leave') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("GrantLeave", function ($scope, $http, $sce) {
        })
        GlobalApp.directive("calender",function () {
            return {
                restrict:'AE',
                scope:{
                    showOnlyCurrentYear:'@',
                    showOnlyCurrentMonth:'@',
                    selectedDates:'=?'
                },
                controller:function ($scope) {
                    $scope.months =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        $scope.selectedDates = [];
                    var currentDate = moment();
                    $scope.currentMonth = {
                        totalDays : currentDate.daysInMonth(),
                        month : currentDate.get('month'),
                        year : currentDate.get('year'),
                        date : currentDate.get('date'),
                    };
                    $scope.previousMonth = {
                        totalDays : moment().date(1).month($scope.currentMonth.month-1).year($scope.currentMonth.year).daysInMonth(),
                        month :  $scope.currentMonth.month-1,
                        year :  $scope.currentMonth.year,
                        date : 1,
                    };
                    $scope.nextMonth = {
                        totalDays :moment().date(1).month($scope.currentMonth.month+1).year($scope.currentMonth.year).daysInMonth(),
                        month : $scope.currentMonth.month+1,
                        year :$scope.currentMonth.year,
                        date : 1,
                    };
                    $scope.next = function (event) {
                        event.preventDefault();
                        var currentDate = moment().date(1).month($scope.nextMonth.month).year($scope.nextMonth.year);
                        $scope.currentMonth = {
                            totalDays : currentDate.daysInMonth(),
                            month : currentDate.get('month'),
                            year : currentDate.get('year'),
                            date : currentDate.get('date'),
                        };
                        $scope.previousMonth = {
                            totalDays : moment().date(1).month($scope.currentMonth.month-1).year($scope.currentMonth.year).daysInMonth(),
                            month :  $scope.currentMonth.month-1,
                            year :  $scope.currentMonth.year,
                            date : 1,
                        };
                        $scope.nextMonth = {
                            totalDays :moment().date(1).month($scope.currentMonth.month+1).year($scope.currentMonth.year).daysInMonth(),
                            month : $scope.currentMonth.month+1,
                            year :$scope.currentMonth.year,
                            date : 1,
                        };
                        $scope.makeCalender();
                    }
                    $scope.previous = function () {

                    }
                    $scope.makeCalender = function() {
                        $scope.calender =  new Array(6);
                        makePreviousCalender();
//                        alert(1)
                        var wd = moment().date(1).month($scope.currentMonth.month).year($scope.currentMonth.year).day()
                        for(var i=0;i<6;i++){
                            if(i*7+1>$scope.currentMonth.totalDays) break;
                            for(var j=0;j<7;j++){
                                if(i*7+j+1<=$scope.currentMonth.totalDays&&j>=wd) {
                                    $scope.calender[i][j] = {
                                        day: i * 7 + j-wd + 1,
                                        tag: "cur"
                                    }
                                } else{
                                    $scope.calender[i][j] = {};
                                }
                            }
                            $scope.calender[i+1] =  new Array(7);
                        }

                        console.log($scope.calender)
                    }
                    function makePreviousCalender() {
                        $scope.calender[0] = new Array(7);
                       /* var lastWeekDay =6 - moment().date($scope.previousMonth.totalDays)
                            .month($scope.previousMonth.month)
                            .year($scope.previousMonth.year).day();
                        console.log(lastWeekDay)
                        for(var i = $scope.previousMonth.totalDays-lastWeekDay;i<$scope.previousMonth.totalDays;i++){
                            $scope.calender[0].push({
                                day:i,tag:"pre"
                            })
                        }*/
                    }
                },
                link:function ( scope,elem, attr) {
                    scope.makeCalender();
                },
                template:`<div class="big-date-picker">
                            <div class="header">
                                <div class="row">
                                    <div class="col-sm-8">
                                       <h3 style="margin: 4px">
                                           [[months[currentMonth.month] ]], [[currentMonth.year]]
                                       </h3>
                                    </div>
                                    <div class="col-sm-4">
                                       <div class="btn-group">
                                           <a href="#" class="btn btn-default">
                                               <i class="fa fa-angle-left"></i>
                                           </a>
                                           <a href="#" class="btn btn-default" ng-click="next($event)">
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
                                    <div class="date" ng-repeat="d in c track by $index" ng-class="{'cursor-pointer':d.tag=='cur','cursor-disabled':d.tag!='cur'}">[[d.day]]</div>
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
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <form >
                            <div class="form-group">
                                <label for="" class="control-label" style="display: block;text-align: center;font-size: 18px">
                                    Enter Ansar ID To Grant Leaves
                                </label>
                                <div class="input-group input-group-lg">

                                    <input type="text" class="form-control " placeholder="Ansar ID">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="col-sm-6 col-centered">
                        <calender></calender>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .cursor-pointer{
            cursor: pointer;
        }
        .cursor-disabled{
            cursor: not-allowed;
        }
        .big-date-picker{
            display: block;
            border:1px solid #cccccc;;
            /*padding: 10px;*/
        }
        .big-date-picker>.header{
            text-align: center;
            padding: 5px 10px;
            font-size: 16px;
            font-weight: bold;
            overflow: hidden;
            border-bottom: 1px solid #cccccc;
            /*height: 50px;*/
        }
        .big-date-picker>.header span{
            display: inline-block;
            vertical-align: middle;
        }
        .big-date-picker>.body>.date-row,.big-date-picker>.body>.week-row{
            display: flex;
        }
        .big-date-picker>.body>.date-row{
            border-top: 1px solid #cccccc;
            border-bottom: 1px solid #cccccc;
        }
        .big-date-picker>.body>.date-row>.date{
            flex: 1;
            height: 50px;
            align-items: center;
            justify-content: center;
            display: flex;
            font-weight: bold;
            border-right:1px solid #cccccc;
        }
        .date-row>.date:not(:first-child){
            /*border-left: none !important;*/
            border-left:1px solid #cccccc;
            border-right: none !important;
        }
        .date-row>.date:first-child{
            border-right: none !important;
        }
        .date-row:not(:nth-child(2)){
            border-top: none !important;
        }
        .date-row:last-child{
            border-bottom: none !important;
        }
        .week-row>.week-title{
            flex: 1;
            font-weight: bold;
            text-align: center;
            padding: 5px 10px;
        }

    </style>
    <script>
    </script>
@endsection
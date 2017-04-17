<!DOCTYPE html>
<html>
<head>
    @include('template.resource')
    <style>
        .table > tbody > tr > td, .table > thead > tr > td, .table > tr > td, .table > tr > th {
            background: rgba(255, 255, 255, .4) !important;
        }
    </style>
    <script>
        var app = angular.module('LoginApp', [], ['$interpolateProvider', function ($interpolateProvider) {
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
        }])
        app.controller('loginController', ['$scope', '$http', function ($scope, $http) {
            $scope.panelData = {
                pcMale: 0,
                pcFemale: 0,
                apcMale: 0,
                apcFemale: 0,
                ansarMale: 0,
                ansarFemale: 0,
            }
            $scope.loading = true;
            $http({
                url: '{{URL::route('central_panel_list')}}',
                method: 'get'
            }).then(function (response) {
                $scope.panelData.pcMale = response.data.pm;
                $scope.panelData.pcFemale = response.data.pf;
                $scope.panelData.apcMale = response.data.apm;
                $scope.panelData.apcFemale = response.data.apf;
                $scope.panelData.ansarMale = response.data.am;
                $scope.panelData.ansarFemale = response.data.af;
                $scope.loading = false;
            }, function (response) {

            })
        }])

        app.directive('loginAttempt', function ($interval) {
            return {
                restrict:'E',
                controller: function ($scope) {
                    $scope.stopTimer = function (timer) {
                        if(angular.isDefined(timer)){
                            $interval.cancel(timer)
                        }
                    }
                },
                scope:{
                  disableId:'@'
                },
                link: function (scope, elem, attrs) {
                    var seconds = parseInt($(elem).html());
                    var timer = $interval(function () {
                        seconds--;
                        $(scope.disableId).prop('disabled',true)
                        $(elem).html(seconds)
                        if(seconds<=0) {
                            scope.stopTimer(timer);
                            timer = undefined;
                            location.reload();
                            $(scope.disableId).prop('disabled',false)
                        }
                    },1000)
                }
            }
        })
    </script>
    <style>
        table > tbody > tr > th {
            background: rgba(255, 255, 255, .5) !important;
        }

        table > tbody > tr {
            background: transparent !important;
        }
    </style>

</head>
<body class="login-page" ng-app="LoginApp">
<div class="login-box" style="margin: 1% auto !important;"  ng-controller="loginController">
    <div class="login-logo">
        <a href="{{URL::to('/')}}" style="color: #ffffff;"><b>Ansar & VDP</b>ERP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body" style="background: rgba(255, 255, 255, 0.32);">
        <p class="login-box-msg" style="color: #000;font-weight: bold">Sign in to start your session</p>
        @if(Session::has('error'))
            <p class="text text-bold text-danger" style="text-align: center;text-transform: uppercase;color:lightyellow">{!! Session::get('error') !!}</p>
        @endif
        <form action="{{action('UserController@handleLogin')}}" method="post">
            {{csrf_field()}}
            <div class="form-group has-feedback">
                <input type="text" name="user_name" class="form-control" value="" placeholder="User Name"/>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-xs-4 col-xs-offset-8">
                    <button id="login" type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <a href="{{URL::route('forget_password_request')}}" style="color: #ffffff;text-transform: uppercase" >I forgot my password</a><br>

    </div>
    <div class="box box-solid"
         style="margin-top: 8px;position: relative;background: rgba(255, 255, 255, 0.32);">
        <div class="overlay" ng-if="loading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
        </div>
        <div class="box-header with-border">
            <h3 class="box-title" id="cccc" data-status="show" style="text-align: center;width: 100%;cursor: pointer">কেন্দ্রীয় প্যানেল তালিকা<img style="width: 27px;padding: 0 5px;vertical-align: top;-webkit-transform: rotate(-180deg);-moz-transform: rotate(-180deg);-ms-transform: rotate(-180deg);-o-transform: rotate(-180deg);transform: rotate(-180deg);" src="{{asset('dist/img/arrow.png')}}"></h3>
        </div>

        <div class="box-body" id="ssss">
            <div class="table-responsive">
                <table class="table table-bordered" style="margin-bottom: 0 !important;">
                    <tr>
                        <th>লিঙ্গ</th>
                        <th style="width: 44%;">পদবী</th>
                        <th>মোটসংখ্যা</th>
                    </tr>
                    <tr>
                        <td rowspan="3">
                            পুরুষ
                        </td>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>3])}}">পিসি</a>
                        </td>
                        <td id="totalPCMale">[[panelData.pcMale]]</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>2])}}">এপিসি</a>
                        </td>
                        <td id="totalAPCMale">[[panelData.apcMale]]</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Male','designation'=>1])}}">আনসার</a>
                        </td>
                        <td id="totalAnsarMale">[[panelData.ansarMale]]</td>
                    </tr>
                    <tr>
                        <td rowspan="3">
                            মহিলা
                        </td>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>3])}}">পিসি </a>
                        </td>
                        <td id="totalPCFeMale">[[panelData.pcFemale]]</td>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>2])}}">এপিসি </a>
                        </td>
                        <td id="totalAPCFeMale">[[panelData.apcFemale]]</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{URL::route('panel_list',['sex'=>'Female','designation'=>1])}}">আনসার</a>
                        </td>
                        <td id="totalAnsarFeMale">[[panelData.ansarFemale]]</td>
                    </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $("#cccc").on('click', function () {
        var b = $(this);
        if ($(this).attr('data-status') == 'show') {
            $("#ssss").slideUp(200, function () {
                b.attr('data-status', 'hide')
                b.children('img').css({
                    transform:'rotate(0deg)',
                    webkitTransform:'rotate(0deg)',
                    msTransform:'rotate(0deg)',
                    oTransform:'rotate(0deg)',
                    mozTransform:'rotate(0deg)',
                    transition:'all .3s'
                })
            })
        }
        else {
            $("#ssss").slideDown(200, function () {
                b.attr('data-status', 'show')
                b.children('img').css({
                    transform:'rotate(-180deg)',
                    webkitTransform:'rotate(-180deg)',
                    msTransform:'rotate(-180deg)',
                    oTransform:'rotate(-180deg)',
                    mozTransform:'rotate(-180deg)',
                    transition:'all .3s'
                })
            })
        }
    })
</script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ansar &amp; VDP ERP</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="shortcut icon" href=" {{asset('dist/img/favicon.ico')}}">
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{asset('dist/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/notify.css')}}" rel="stylesheet"
          type="text/css"/>
    <!-- Ionicons -->
    <link href="{{asset('dist/css/ionicons.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/animate.css')}}" rel="stylesheet" type="text/css">
    <!-- Theme style -->
    <link href="{{asset('dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>

    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="{{asset('dist/css/skins/_all-skins.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/user_css.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/session.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/entryform.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/id-card.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/print.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/print_bootstrap.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/iCheck/square/blue.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/bank-form.css')}}" rel="stylesheet" type="text/css"/>

    <script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/jQueryUI/jquery-ui.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/js/angular.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/js/datePicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/js/sortTable.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/js/notify.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/js/alertify.js')}}" type="text/javascript"></script>
    <script src="{{asset('dist/js/ajaxsubmit.js')}}"></script>
    <script src="{{asset('dist/js/angular-filter.js')}}"></script>
    {{--<script src="{{asset('dist/js/moment.min.js')}}"></script>--}}
    <script src="{{asset('dist/js/moment-locales.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
//            $('.sidebar-menu li').hover(function () {
//                var p = $(this);
//                if (p.has('ul').length > 0) p.addClass('arrow-left');
//                $(this).children('ul').css('display', 'block')
//                $(this).children('ul').position({
//                    "of": p,
//                    "at": "right+5 top",
//                    "my": "left top",
//                    "collision": "fit fit"
//                })
//            }, function () {
//                $(this).children('ul').css('display', 'none')
//                $(this).removeClass('arrow-left');
//            })
            $('#national_id_no,#birth_certificate_no,#mobile_no_self').keypress(function (e) {
                var code = e.keyCode ? e.keyCode : e.which;
//                alert(code)
                if ((code >= 47 && code <= 57) || code == 8);
                else e.preventDefault();
            });
        });

        var GlobalApp = angular.module('GlobalApp', ['angular.filter'], function ($interpolateProvider, $httpProvider) {
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
            $httpProvider.useApplyAsync(true)
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
            $httpProvider.interceptors.push(function () {
                return {
                    response: function (response) {
                        if (response.data.status == 'logout') {
                            location.assign(response.data.loc);
                            return;
                        }
                        else if (response.data.status == 'forbidden') {

                        }
                        return response;
                    }
                }
            })
        });

        GlobalApp.controller('MenuController', function ($scope) {
            $scope.menu = [];
            var permission = '{{auth()->user()->userPermission->permission_list?auth()->user()->userPermission->permission_list:""}}'
            var p_type = parseInt('{{auth()->user()->userPermission->permission_type}}')
            if (permission)$scope.menu = JSON.parse(permission.replace(/&quot;/g, '"'))
            //alert($scope.menu.indexOf('reduce_guard_strength')>=0||p_type==1)
            $scope.checkMenu = function (value) {
                return $scope.menu.indexOf(value) >= 0 || p_type == 1
            }
        })
    </script>
    <script src="{{asset('dist/js/app.min.js')}}" type="text/javascript"></script>


</head>
<body class="skin-blue sidebar-mini " ng-app="GlobalApp"><!-- ./wrapper -->

<div class="wrapper" ng-app="GlobalApp">
    <header class="main-header">
        <!-- Logo -->
        <a href="{{URL::to('/')}}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">ERP</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Ansar & VDP</b> ERP</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    {{--<li class="dropdown messages-menu">--}}
                    {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                    {{--<i class="fa fa-envelope-o"></i>--}}
                    {{--<span class="label label-success">4</span>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu">--}}
                    {{--<li class="header">You have 4 messages</li>--}}
                    {{--<li>--}}
                    {{--<!-- inner menu: contains the actual data -->--}}
                    {{--<ul class="menu">--}}
                    {{--<li><!-- start message -->--}}
                    {{--<a href="#">--}}
                    {{--<div class="pull-left">--}}
                    {{--<img src="dist/img/user2-160x160.jpg" class="img-circle"--}}
                    {{--alt="User Image"/>--}}
                    {{--</div>--}}
                    {{--<h4>--}}
                    {{--Support Team--}}
                    {{--<small><i class="fa fa-clock-o"></i> 5 mins</small>--}}
                    {{--</h4>--}}
                    {{--<p>Why not buy a new awesome theme?</p>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    {{--<!-- end message -->--}}
                    {{--<li>--}}
                    {{--<a href="#">--}}
                    {{--<div class="pull-left">--}}
                    {{--<img src="dist/img/user3-128x128.jpg" class="img-circle"--}}
                    {{--alt="User Image"/>--}}
                    {{--</div>--}}
                    {{--<h4>--}}
                    {{--Admin Team--}}
                    {{--<small><i class="fa fa-clock-o"></i> 2 hours</small>--}}
                    {{--</h4>--}}
                    {{--<p>Why not buy a new awesome theme?</p>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                    {{--<a href="#">--}}
                    {{--<div class="pull-left">--}}
                    {{--<img src="dist/img/user4-128x128.jpg" class="img-circle"--}}
                    {{--alt="User Image"/>--}}
                    {{--</div>--}}
                    {{--<h4>--}}
                    {{--Developers--}}
                    {{--<small><i class="fa fa-clock-o"></i> Today</small>--}}
                    {{--</h4>--}}
                    {{--<p>Why not buy a new awesome theme?</p>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                    {{--<a href="#">--}}
                    {{--<div class="pull-left">--}}
                    {{--<img src="dist/img/user3-128x128.jpg" class="img-circle"--}}
                    {{--alt="User Image"/>--}}
                    {{--</div>--}}
                    {{--<h4>--}}
                    {{--Sales Department--}}
                    {{--<small><i class="fa fa-clock-o"></i> Yesterday</small>--}}
                    {{--</h4>--}}
                    {{--<p>Why not buy a new awesome theme?</p>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                    {{--<a href="#">--}}
                    {{--<div class="pull-left">--}}
                    {{--<img src="dist/img/user4-128x128.jpg" class="img-circle"--}}
                    {{--alt="User Image"/>--}}
                    {{--</div>--}}
                    {{--<h4>--}}
                    {{--Reviewers--}}
                    {{--<small><i class="fa fa-clock-o"></i> 2 days</small>--}}
                    {{--</h4>--}}
                    {{--<p>Why not buy a new awesome theme?</p>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    {{--</ul>--}}
                    {{--</li>--}}
                    {{--<li class="footer"><a href="#">See All Messages</a></li>--}}
                    {{--</ul>--}}
                    {{--</li>--}}
                    <!-- Notifications: style can be found in dropdown.less -->
                    @if(auth()->user()->type==11)
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning">{{Notification::getTotal()}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have {{Notification::getTotal()}} forget password notifications
                                </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        @foreach(Notification::getNotification() as $notification)
                                            <li>
                                                <a href="#" style="white-space: normal !important;overflow: auto !important;text-overflow: initial !important;">
                                                    <i class="fa fa-users text-aqua"></i>
                                                    <span style="color: #000000;font-size: 1.3em;">{{$notification->user_name}}</span>
                                                    forgets password. Change his password
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="footer"><a href="{{URL::to('all_notification')}}">View all</a></li>
                            </ul>
                        </li>
                        @endif
                                <!-- Tasks: style can be found in dropdown.less -->
                        {{--<li class="dropdown tasks-menu">--}}
                        {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="fa fa-flag-o"></i>--}}
                        {{--<span class="label label-danger">9</span>--}}
                        {{--</a>--}}
                        {{--<ul class="dropdown-menu">--}}
                        {{--<li class="header">You have 9 tasks</li>--}}
                        {{--<li>--}}
                        {{--<!-- inner menu: contains the actual data -->--}}
                        {{--<ul class="menu">--}}
                        {{--<li><!-- Task item -->--}}
                        {{--<a href="#">--}}
                        {{--<h3>--}}
                        {{--Study and work as a sainik--}}
                        {{--<small class="pull-right">20%</small>--}}
                        {{--</h3>--}}
                        {{--<div class="progress xs">--}}
                        {{--<div class="progress-bar progress-bar-aqua" style="width: 20%"--}}
                        {{--role="progressbar" aria-valuenow="20" aria-valuemin="0"--}}
                        {{--aria-valuemax="100">--}}
                        {{--<span class="sr-only">20% Complete</span>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--<!-- end task item -->--}}
                        {{--<li><!-- Task item -->--}}
                        {{--<a href="#">--}}
                        {{--<h3>--}}
                        {{--Works at Ministry of Health--}}
                        {{--<small class="pull-right">40%</small>--}}
                        {{--</h3>--}}
                        {{--<div class="progress xs">--}}
                        {{--<div class="progress-bar progress-bar-green" style="width: 40%"--}}
                        {{--role="progressbar" aria-valuenow="20" aria-valuemin="0"--}}
                        {{--aria-valuemax="100">--}}
                        {{--<span class="sr-only">40% Complete</span>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--<!-- end task item -->--}}
                        {{--<li><!-- Task item -->--}}
                        {{--<a href="#">--}}
                        {{--<h3>--}}
                        {{--Work with integrity for public security--}}
                        {{--<small class="pull-right">60%</small>--}}
                        {{--</h3>--}}
                        {{--<div class="progress xs">--}}
                        {{--<div class="progress-bar progress-bar-red" style="width: 60%"--}}
                        {{--role="progressbar" aria-valuenow="20" aria-valuemin="0"--}}
                        {{--aria-valuemax="100">--}}
                        {{--<span class="sr-only">60% Complete</span>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--<!-- end task item -->--}}
                        {{--<li><!-- Task item -->--}}
                        {{--<a href="#">--}}
                        {{--<h3>--}}
                        {{--Work with courage--}}
                        {{--<small class="pull-right">80%</small>--}}
                        {{--</h3>--}}
                        {{--<div class="progress xs">--}}
                        {{--<div class="progress-bar progress-bar-yellow" style="width: 80%"--}}
                        {{--role="progressbar" aria-valuenow="20" aria-valuemin="0"--}}
                        {{--aria-valuemax="100">--}}
                        {{--<span class="sr-only">80% Complete</span>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--<!-- end task item -->--}}
                        {{--</ul>--}}
                        {{--</li>--}}
                        {{--<li class="footer">--}}
                        {{--<a href="#">View all tasks</a>--}}
                        {{--</li>--}}
                        {{--</ul>--}}
                        {{--</li>--}}
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img
                                        src="{{action('UserController@getImage',['file'=>auth()->user()->userProfile->profile_image])}}"
                                        class="user-image" alt="User Image"/>
                                <span class="hidden-xs">{{Auth::user()->userProfile->first_name.' '.Auth::user()->userProfile->last_name}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="{{action('UserController@getImage',['file'=>auth()->user()->userProfile->profile_image])}}"
                                         class="img-circle" alt="User Image"/>

                                    <p>
                                        {{Auth::user()->userProfile->first_name.' '.Auth::user()->userProfile->last_name}}
                                        <br>
                                        {{Auth::user()->userProfile->rank}}
                                        {{--<small>Member since Nov. 2012</small>--}}
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left"><a
                                                href="{{request()->route()?(request()->route()->getPrefix()?URL::to(request()->route()->getPrefix()."/view_profile",['id'=>Auth::user()->id]):URL::to('view_profile',['id'=>Auth::user()->id])):URL::to('view_profile',['id'=>Auth::user()->id])}}"
                                                class="btn btn-default btn-flat">Profile</a></div>
                                    <div class="pull-right"><a href="{{action('UserController@logout')}}"
                                                               class="btn btn-default btn-flat">Sign out</a></div>
                                </li>
                            </ul>
                        </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    @if(is_null(request()->route()))
        @include('template.menu')
    @elseif(is_null(request()->route())||empty(request()->route()->getPrefix()))
        @include('template.menu')
    @elseif(strcasecmp(request()->route()->getPrefix(),'SD')==0)
        @include('SD::menu')
    @elseif(strcasecmp(request()->route()->getPrefix(),'HRM')==0)
        @include('HRM::menu')
    @endif
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('title')
                <small>@yield('small_title')</small>
            </h1>
            @yield('breadcrumb')
        </section>
        @yield('content')
    </div>


    <footer class="main-footer">
        <div class="pull-right hidden-xs"><b>Developed by</b> <a href="#">shurjoMukhi</a></div>
        <strong>Copyright &copy; 2015 <a href="#">Ansar & VDP</a></strong> All rights reserved.
    </footer>


</div>
</body>
</html>


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
    <script src="{{asset('dist/js/jquery.cookie.js')}}"></script>
    {{--<script src="{{asset('dist/js/moment.min.js')}}"></script>--}}
    <script src="{{asset('dist/js/moment-locales.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
            resizeMenu();
            $('#national_id_no,#birth_certificate_no,#mobile_no_self').keypress(function (e) {
                var code = e.keyCode ? e.keyCode : e.which;
                if ((code >= 47 && code <= 57) || code == 8);
                else e.preventDefault();
            });
            $(window).resize(function (e) {
                resizeMenu();
            })
            $(".navbar-custom-menu").resize(function () {
                alert(2222);
            })
            function resizeMenu() {
                console.log({width: $("#ncm").outerWidth(true)})
                var w = $("#resize_menu").width();
                var cw = 0;
                $("#resize_menu").children().not('h4').each(function (ch) {
                    cw += $(this).outerWidth(true);
                })
                $("#resize_menu").children('h4').width(w - cw - 20);
            }

            var lastWidth = $("#ncm").outerWidth(true);
            setInterval(function () {
                var v = $("#ncm");
                if (lastWidth == v.outerWidth(true)) return;
                lastWidth = v.outerWidth(true);
                console.log({change: lastWidth})
                resizeMenu();
            }, 100)
        });

        var GlobalApp = angular.module('GlobalApp', ['angular.filter'], function ($interpolateProvider, $httpProvider) {
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
            $httpProvider.useApplyAsync(true)
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
            $httpProvider.interceptors.push(function ($q,$injector) {
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
                                retryHttpRequest(response.config, d);
                                return d.promise;
                        }
                        return $q.reject(response);
                    }
                }
                function retryHttpRequest(config, deferred) {
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
<div class="wrapper" ng-cloak>
    <header class="main-header">
        <!-- Logo -->
        <a href="{{URL::to('/')}}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">ERP</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Ansar & VDP</b> ERP</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav id="resize_menu" class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <h4 class="header-title">@yield('title')</h4>

            <div id="ncm" class="navbar-custom-menu">
                <ul class="nav navbar-nav">
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
                                                <a href="{{URL::to((request()->route()?request()->route()->getPrefix():'')."/change_password/".$notification->user_name)}}"
                                                   style="white-space: normal !important;overflow: auto !important;text-overflow: initial !important;">
                                                    <i class="fa fa-users text-aqua"></i>
                                                    <span style="color: #000000;font-size: 1.3em;">{{$notification->user_name}}</span>
                                                    forgets password.
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="footer"><a
                                            href="{{URL::to((request()->route()?request()->route()->getPrefix():'')."/all_notification")}}">View
                                        all</a></li>
                            </ul>
                        </li>
                    @endif
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

                                <p style="color: #666666">
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
        <section class="content-header sh">
            @yield('breadcrumb')
            <h1 class="small-title">
                <small class="small-title">@yield('small_title')</small>
            </h1>
        </section>
        @yield('content')
    </div>


    <footer class="main-footer">
        <div class="pull-right hidden-xs"><b>Developed by</b> <a href="#">shurjoMukhi</a></div>
        <strong>2015 &copy; <a href="#">Ansar & VDP</a></strong> All rights reserved.
    </footer>

    <script>
        $(document).ready(function (e) {
            var url = '{{request()->url()}}'
            var p = $('a[href="' + url + '"]');
            if (p.length > 0) {
                //console.log({beforeurl:$.cookie('ftt')})
                $.cookie('ftt', null);
                $.cookie('ftt', url);

                console.log({afterurl: $.cookie()})
                //console.log({afterurl:url})
            }
            else {
                var s = $.cookie();
                p = $('a[href="' + s.ftt + '"]')
                console.log($.cookie())
                console.log({sss: s.ftt})
            }
            //alert(p.text())
            if (p.parents('.sidebar-menu').length > 0) {
                p.parents('li').eq(0).parents('ul').eq(0).addClass('menu-open').css('display', 'block');
                if (p.parents('li').length > 1) {
                    if (p.parents('li').parents('ol').length <= 0)p.parents('li').eq(0).addClass('active-submenu');
                    p.parents('li').not(':eq(0)').addClass('active');
                }
                else {
                    p.parents('li').addClass('active');
                }
            }
        })
    </script>
</div>
</body>
</html>


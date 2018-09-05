<!DOCTYPE html>
<html>
<head>
    @include('template.resource')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            })
            $(window).load(function () {
                var text = $.trim($(".module-menu-container > ul.module-menu>li.active>a").text());
                $(".module-menu-container > .module-small-header > .header-content").text(text ? text : "ERP");
            })
            $('#national_id_no,#birth_certificate_no,#mobile_no_self').keypress(function (e) {
                var code = e.keyCode ? e.keyCode : e.which;
                if ((code >= 47 && code <= 57) || code == 8) ;
                else e.preventDefault();
            });
            $(".module-small-header").on('click', function (e) {
                $(".module-menu:not('.still')").slideToggle(200, function () {
                    $(this).addClass('still');
                    $(".module-small-header>.icon>i").addClass('fa-angle-up').removeClass('fa-angle-down')
                })
                $(".module-menu.still").slideToggle(200, function () {
                    $(this).removeClass('still');
                    $(".module-small-header>.icon>i").addClass('fa-angle-down').removeClass('fa-angle-up')
                })
            })
            $(window).resize(function () {
                if ($(this).width() > 864) {
                    $(".module-menu").removeAttr('style')
                    $(".module-menu").removeClass('still')
                    $(".module-small-header>.icon>i").addClass('fa-angle-down').removeClass('fa-angle-up')
                }
            })
        });

        GlobalApp.controller('MenuController', function ($scope, $rootScope) {
            $scope.menu = [];
            var permission = '{{auth()->user()->userPermission->permission_list?auth()->user()->userPermission->permission_list:""}}'
            var p_type = parseInt('{{auth()->user()->userPermission->permission_type}}')
            if (permission) $scope.menu = JSON.parse(permission.replace(/&quot;/g, '"'))
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
    <div class="header-top">
        <div class="logo">
            <a href="{{URL::route('home')}}"><img src="{{asset('dist/img/erp-logo.png')}}" class="img-responsive"
                                                  alt=""></a>
        </div>
        <div class="middle_header_logo">
            <img src="{{asset('dist/img/erp-hdeader.png')}}" class="img-responsive" alt="" width="400" height="400">
        </div>
        <div class="clearfix"></div>

    </div>
    <header class="main-header">
        <!-- Logo -->
        <?php $title = request()->route() ? request()->route()->getPrefix() : ''; ?>
        <a href="{{URL::to("/{$title}")}}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <?php $title = request()->route() ? request()->route()->getPrefix() : ''; ?>
            <span class="logo-mini">{{config("app.title_mini_{$title}")}}</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg" style="font-size: 16px;"><b>{{config("app.title_lg_{$title}.title")}}</b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav id="resize_menu" class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="module-menu-container">
                <div class="module-small-header">
                    <span class="header-content">HRM</span>
                    <span class="icon"><i class="fa fa-angle-down"></i></span>
                </div>
                <ul class="module-menu">
                    @foreach(config('app.modules') as $module)
                        <li><a href="{{URL::to($module['route'])}}"
                               @if(!is_null(request()->route())&&strcasecmp(request()->route()->getPrefix(),$module['name'])==0) class="active" @endif>{{$module['name']}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div id="ncm" class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    @if(auth()->user()->type==11)
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning">{{Notification::getForgetPasswordNotificationTotal()}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have {{Notification::getForgetPasswordNotificationTotal()}} forget password notifications
                                </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        @foreach(Notification::getForgetPasswordNotification() as $notification)
                                            <li>
                                                <a href="{{URL::to("/change_password/".$notification->user_name)}}"
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
                                            href="{{URL::to("/all_notification")}}">View
                                        all</a></li>
                            </ul>
                        </li>
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user"></i>
                                <span class="label label-warning">{{Notification::getTotalUserRequest()}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have {{Notification::getTotalUserRequest()}} user create notifications
                                </li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        @foreach(Notification::getUserRequestNotification() as $notification)
                                            <li>
                                                <a href="{{URL::to("/all_user_request_notification/".$notification->id)}}"
                                                   style="white-space: normal !important;overflow: auto !important;text-overflow: initial !important;display: block">
                                                    <i class="fa fa-users text-aqua"></i>
                                                    <span style="color: #000000;font-size: 1.3em;">{{$notification->user->user_name}}</span>
                                                    make a user create request.<span class="label label-info">{{implode(" ",explode('_',$notification->user_type))}}</span>
                                                </a>

                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="footer"><a
                                            href="{{URL::to("/all_user_request_notification")}}">View
                                        all</a></li>
                            </ul>
                        </li>
                    @endif

                    <li class="dropdown user user-menu"><a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img
                                    src="{{action('UserController@getImage',['file'=>auth()->user()->userProfile->profile_image])}}"
                                    class="user-image" alt="User Image"/>
                            <span class="hidden-xs">{{Auth::user()->userProfile->getFullName()}}</span>
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
    @else
        @include(request()->route()->getPrefix().'::menu')
    @endif
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header sh">
            <h3 class="header-title">@yield('title')</h3>
            @yield('breadcrumb')
            <h1 class="small-title">
                <small class="small-title">@yield('small_title')</small>
            </h1>
        </section>
        <div class="fade" ng-if="loadingView" ng-class="{in:loadingView}"
             style="position: absolute;width:100%;height: 100%;z-index:10;background: #ffffff">
            <div style="position: absolute;margin-top: 25%;margin-left: 50%;transform: translate(-50%,-50%);font-size: 1.2em">
                <i class="fa fa-spinner fa-pulse fa-2x"></i>&nbsp;&nbsp;<span class="text text-bold"
                                                                              style="vertical-align: super">
                    LOADING......
                </span>
            </div>
        </div>
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
            }
            else {
                var s = $.cookie();
                p = $('a[href="' + s.ftt + '"]')
            }
            //alert(p.text())
            if (p.parents('.sidebar-menu').length > 0) {
//                p.parents('li').eq(0).parents('ul').eq(0).addClass('menu-open').css('display', 'block');
                //alert(p.parents('li').eq(1).html())
                if (p.parents('li').length > 1 && p.parents('.module-menu').length <= 0) {
                    if (p.parents('li').parents('ol').length <= 0) {
                        p.parents('li').eq(0).addClass('active-submenu');
                    }
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


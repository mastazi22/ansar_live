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
            $('.sidebar-menu li').hover(function () {
                var p = $(this);
                if (p.has('ul').length > 0) p.addClass('arrow-left');
                $(this).children('ul').css('display', 'block')
                $(this).children('ul').position({
                    "of": p,
                    "at": "right+5 top",
                    "my": "left top",
                    "collision": "fit fit"
                })
            }, function () {
                $(this).children('ul').css('display', 'none')
                $(this).removeClass('arrow-left');
            })
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
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
            $httpProvider.interceptors.push(function () {
                return {
                    response: function (response) {
                        if (response.data.status == 'logout') {
                            location.assign(response.data.loc);
                            return;
                        }
                        return response;
                    }
                }
            })
        });


    </script>
    <script src="{{asset('dist/js/app.min.js')}}" type="text/javascript"></script>


</head>
<body class="skin-blue sidebar-mini" ng-app="GlobalApp"><!-- ./wrapper -->
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="../home.html" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">ERP</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Ansar & VDP</b> ERP</span> </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>

            <div class="navbar-custom-menu">

            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar" ng-controller="MenuController">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel" style="margin-top: 10px;margin-bottom: 0;">

                <div class="pull-left" style="color: #FFFFFF;font-size: 16px;text-align: center">
                    <p style="padding: 0 !important;margin: 0;line-height: 1">Human Resource Management</p>

                    <p style="padding: 0 !important;margin: 0;">(HRM)</p>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
        </section>
        <!-- /.sidebar -->
    </aside>
    @yield('content')

    <footer class="main-footer">
        <div class="pull-right hidden-xs"><b>Developed by</b> <a href="#">shurjoMukhi</a></div>
        <strong>Copyright &copy; 2015 <a href="#">Ansar & VDP</a></strong> All rights reserved.
    </footer>


</div>
</body>
</html>


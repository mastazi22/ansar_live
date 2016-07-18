<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ansar &amp; VDP ERP</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href=" {{asset('dist/img/favicon.ico')}}">
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Ionicons -->
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/animate.css')}}" rel="stylesheet" type="text/css">
    <!-- Theme style -->
    <link href="{{asset('dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="{{asset('dist/css/skins/_all-skins.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/iCheck/square/blue.css')}}" rel="stylesheet" type="text/css"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .sidebar-menu-submenu {
            position: absolute;
            left: 100%;
            top: 0;
            margin-left: 5px;
            width: 230px;
            z-index: 5;
            background-color: #222d32;
            display: none;
        }

        .sidebar-menu-submenu::before {
            content: '';
            width: 0;
            height: 0;
            border-right: 8px solid #0000C2;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            position: absolute;
            top: 10px;
            right: 100%;
        }

        .sidebar-menu {
            overflow: visible !important;
        }

        .line-bar-top {
            position: absolute;
            width: 15px;
            top: 64%;
            left: 0;
            height: 70%;
            border-top: 2px solid #306754;
            border-left: 2px solid #306754;
        }

        .line-bar-top::after {
            content: '';
            position: absolute;
            width: 15px;
            bottom: 0;
            height: 2px;
            background-color: #306754;
            left: -15px;

        }

        .line-bar-bottom {
            position: absolute;
            width: 15px;
            top: -30%;
            left: 0;
            height: 70%;
            border-bottom: 2px solid #306754;
            border-left: 2px solid #306754;
        }

        .line-bar-bottom::after {
            content: '';
            position: absolute;
            width: 15px;
            top: 0;
            height: 2px;
            background-color: #306754;
            left: -15px;

        }

        .line-bar-middle::before {
            content: '';
            width: 30px;
            top: 52%;
            height: 2px;
            background-color: #306754;
            left: -15px;
            position: absolute;
        }

        .custom-table tr:first-child > td {
            border: none !important;
        }

        #loading-box {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: rgba(171, 171, 171, 0.26);
            background-image: url("{{asset('dist/img/facebook.gif')}}");
            background-repeat: no-repeat;
            background-position: center center;
        }
    </style>

</head>
<body class="login-page">
<div class="login-box" style="margin: 1% auto !important;">
    <div class="login-logo">
        <a href="home.html"><b>Ansar & VDP</b>ERP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        {{--<p class="login-box-msg">Sign in to start your session</p>--}}
        @if(Session::has('error'))
            <p class="text text-danger"
               style="text-align: center;text-transform: uppercase">{{Session::get('error')}}</p>
        @endif
        @if(Session::has('success'))
            <p class="text text-success"
               style="text-align: center;text-transform: uppercase">{{Session::get('error')}}</p>
        @endif
        <form action="{{URL::route('forget_password_request_handle')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label>Enter your user name</label>
                <input type="text" name="user_name" class="form-control" value="" placeholder="User Name"/>
                @if($errors->has('user_name'))
                    <p class="text text-danger">{{$errors->first('user_name')}}</p>
                @endif
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-flat">Submit password change request</button>
        </form>
        <a class="btn btn-link" href="{{URL::route('login')}}" style="padding-left: 0"><i class="fa fa-angle-left"></i> &nbsp;&nbsp;Back to login</a>
    </div>

</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}" type="text/javascript"></script>
<script src="{{asset('bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}" type="text/javascript"></script>

</body>
</html>

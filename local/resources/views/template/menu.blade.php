<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            {{--<li class="header">MAIN NAVIGATION</li>--}}
            <li class="treeview">
                <a href="{{URL::to('/')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>

            </li>

            <li>
                <a href="{{URL::to('HRM')}}">
                    <i class="fa fa-users"></i>
                    <span>Human Resource Management</span>
                </a>
            </li>
            <li class="disable_menu">
                <a href="{{URL::to('SD')}}">
                    <i class="fa  fa-money"></i>
                    <span>Salary Disbursement</span>
                </a>
            </li>
            <li class="disable_menu">
                <a href="#">
                    <i class="fa fa-gears"></i>
                <span>Ansar Deployment Application<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Processing System</span>
                </a>
            </li>
            <li>
                <a href="{{URL::to('recruitment')}}">
                    <i class="fa fa-user"></i>
                    <span>{{ucfirst('ansar recruitment')}}</span>
                </a>
            </li>
            @if(auth()->user()->type==11)

                <li>
                    <a href="{{URL::to('user_management')}}">
                        <i class="fa fa-user"></i>
                        <span>Manage User</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->type==22)

                <li>
                    <a href="{{URL::route('user_create_request.index')}}">
                        <i class="fa fa-user"></i>
                        <span>Manage User</span>
                    </a>
                </li>
            @endif
            {{--<li>--}}
                {{--<a href="pages/calendar.html">--}}
                    {{--<i class="fa fa-calendar"></i> <span>Calendar</span>--}}
                    {{--<small class="label pull-right bg-red">3</small>--}}
                {{--</a>--}}
            {{--</li>--}}
            {{--<li>--}}
                {{--<a href="pages/mailbox/mailbox.html">--}}
                    {{--<i class="fa fa-envelope"></i> <span>Mailbox</span>--}}
                    {{--<small class="label pull-right bg-yellow">12</small>--}}
                {{--</a>--}}
            {{--</li>--}}
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
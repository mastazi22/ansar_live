{{--<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="margin-top: 10px;margin-bottom: 0;">

            <div style="color: #FFFFFF;font-size: 16px;text-align: center">
                <p class="full-header" style="padding: 0 !important;margin: 0;line-height: 1">Salary Disbursement</p>

                <p style="padding: 0 !important;margin: 0;">(SD)</p>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="active treeview">
                <a href="home.html">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>

            </li>

            <li>
                <a href="pages/hrm.html">
                    <i class="fa fa-file"></i>
                    <span>Demand Sheet</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="{{URL::to('SD/demandconstant')}}">
                            <i class="fa fa-cog"></i>Demand Constant
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('SD/demandsheet')}}"><i class="fa fa-file-pdf-o"></i>Generate Demand Sheet</a>
                    </li>
                    <li>
                        <a href="{{URL::to('SD/demandhistory')}}"><i class="fa fa-history"></i>Demand History</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{URL::to('SD')}}">
                    <i class="fa  fa-money"></i>
                    <span>Kpi Account</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="index.html">
                            <i class="fa fa-bank"></i>Kpi Account Details
                        </a>
                    </li>
                    <li>
                        <a href="index2.html"><i class="fa fa-history"></i>Kpi Payment History</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{URL::to('SD')}}">
                    <i class="fa  fa-money"></i>
                    <span>Salary Management</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="index.html">
                            <i class="fa fa-calendar"></i>Ansar Attendance
                        </a>
                    </li>
                    <li>
                        <a href="index2.html"><i class="fa fa-file-excel-o"></i>Salary Sheet Generation</a>
                    </li>
                    <li>
                        <a href="index2.html"><i class="fa fa-history"></i>Salary History</a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>--}}
<aside class="main-sidebar" ng-controller="MenuController">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <ul class="sidebar-menu">
            <li>
                <a href="{{URL::to('/SD')}}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @include('HRM::Partial_view.partial_menu',['menus'=>config('menu.sd')])
        </ul>
    </section>
    <!-- /.sidebar -->
    <script>
        $(window).load(function(){
            var l = $('.sidebar-menu').children('li');
            function removeMenu(m){

                m.each(function () {
                    //console.log({parent: $.trim($(this).parents('li').eq($(this).parents('li').length-1).children('a').text()),children: m.text()})
                    //alert($(this).children('ul').length+" "+$(this).children('ul').children('li').length)
                    if($(this).children('ul').length>0) {
                        if ($(this).children('ul').children('li').length > 0) {
                            removeMenu($(this).children('ul').children('li'));
                        }
                        else if ($(this).children('ul').children('li').length <= 0) {
                            // alert(m.length)
                            $(this).remove();
                        }
                    }
                })
            }
            removeMenu(l)
        })
    </script>
</aside>
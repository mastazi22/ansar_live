<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Md. Nazim Uddin</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
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
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
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
</aside>
<aside class="main-sidebar" ng-controller="MenuController">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="margin-top: 10px;margin-bottom: 0;">

            <div style="color: #FFFFFF;font-size: 16px;text-align: center">
                <p class="full-header" style="padding: 0 !important;margin: 0;line-height: 1">Human Resource Management</p>

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
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li>
                <a href="{{URL::to('HRM')}}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>KPI Branch</span>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('kpi_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>KPI Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('ansar-withdraw-view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Withdraw</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu(ansar_before_withdraw_view)">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Before Withdraw List</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('reduce_guard_strength')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Reduce Guard Strength</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('ansar_before_reduce_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Before Reduce List</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('kpi-withdraw-view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>KPI Withdraw</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('withdrawn_kpi_list')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>KPI Withdraw Date Update</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('inactive_kpi_list')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Inactive KPI List</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('kpi_withdraw_cancel_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>KPI Withdraw Cancel</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Personal info</span>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('anser_list')">
                        <a href="{{URL::to('HRM/entrylist')}}">
                            <i class="fa fa-users"></i>
                            <span>Entry</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Verify Entry(Chunk)</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('entry_draft')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Draft entry</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('entry_advanced_search')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Advanced search</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('orginal_info')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Orginal Info</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('print_card_id_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Print Id Card</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Service</span>
                </a>
                <ul class="treeview-menu">
                    {{--<li>--}}
                    {{--<a href="#">--}}
                    {{--<i class="fa fa-users"></i>--}}
                    {{--<span>Bank Receipt</span>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    <li ng-if="checkMenu('view_panel_list')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Panel</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('make_offer')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Offer</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('offer_quota')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Offer Quota</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Embodiment</span>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('go_to_new_embodiment_page')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Embodiment Entry</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('go_to_new_disembodiment_page')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Dis-Embodiment</span>
                                </a>
                            </li>
                            <?php $user_type = Auth::user()->type;
                            if($user_type == 11 || $user_type == 33){
                            ?>
                            <li ng-if="checkMenu('service_extension_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Service Extension</span>
                                </a>
                            </li>
                            <?php
                            }
                            ?>
                            <li ng-if="checkMenu('disembodiment_date_correction_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Disembodiment Date Correction</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('embodiment_memorandum_id_correction_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Embodiment Mem. ID Correction</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Freeze</span>
                                </a>
                                <ul class="treeview-menu">
                                    <li ng-if="checkMenu('freeze_view')">
                                        <a href="#">
                                            <i class="fa fa-users"></i>
                                            <span>Freeze for Disciplinary Action</span>
                                        </a>
                                    </li>
                                    <li ng-if="checkMenu('freeze_list')">
                                        <a href="#">
                                            <i class="fa fa-users"></i>
                                            <span>After Result of Freeze</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>BlackList</span>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('blacklist_entry_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blacklist</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('unblacklist_entry_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Remove Ansar from Blacklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>BlockList</span>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('blocklist_entry_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blocklist</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('unblocklist_entry_view')">
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Remove Ansar from Blocklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li ng-if="checkMenu('transfer_process')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Transfer</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-bar-chart"></i>
                    <span>Report</span>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('guard_report')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>View Ansar in Guard</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('transfer_ansar_history')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Transfer History</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('view_ansar_service_record')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Service Record</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('ansar_service_report_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>View Ansar Service Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('embodiment_report_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>View Embodied Ansar Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('disembodiment_report_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>View Disembodied Ansar Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('blocklist_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Blocklist Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('blacklist_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Blacklist Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('three_year_over_report_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Three Years Over List</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('service_record_unitwise_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar Service Record Unit Wise</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('transfer_letter_view')">
                        <a href="#">
                            <i class="fa fa-envelope"></i>
                            <span>Print Transfer Letter</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('transfer_letter_view')">
                        <a href="#">
                            <i class="fa fa-envelope"></i>
                            <span>Offer Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('embodiment_letter_view')">
                        <a href="#">
                            <i class="fa fa-envelope"></i>
                            <span>Print Embodiment Letter</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('disembodiment_letter_view')">
                        <a href="#">
                            <i class="fa fa-envelope"></i>
                            <span>Print Dis-Embodiment Letter</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li style="@if(!(Auth::user()->type==11||Auth::user()->type==33)) display:none @endif">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>DG Forms</span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct offer</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct offer from service info</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct Panel</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct Cancel Panel</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct Freeze for DG</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct Embodiment</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct Dis-embodiment</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-send"></i>
                            <span>Direct Transfer</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct BlockList</span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blocklist</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Remove Ansar from Blocklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct BlackList</span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blacklist</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Remove Ansar from Blacklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct offer cancel</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li style="@if(Auth::user()->type!=11) display:none @endif">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Admin</span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Manage User</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Global Parameter</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Cancel Offer</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Ansar id list</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Rejected offer list</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>General Setiings</span>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('view_session_list')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Session Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('unit_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Unit Setting</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('thana_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Thana Setting</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('disease_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Disease Setting</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('skill_view')">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Skill Setting</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
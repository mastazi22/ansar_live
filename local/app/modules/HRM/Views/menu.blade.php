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
        {{--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>--}}
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            {{--<li class="header">MAIN NAVIGATION</li>--}}
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
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('kpi_view')">
                        <a href="{{URL::route('kpi_view')}}">
                            <i class="fa fa-users"></i>
                            <span>KPI Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('ansar-withdraw-view')">
                        <a href="{{URL::route('ansar-withdraw-view')}}">
                            <i class="fa fa-users"></i>
                            <span>Withdraw Ansar</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu(ansar_before_withdraw_view)">
                        <a href="{{URL::route('ansar_before_withdraw_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar List Before Withdrawal</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('reduce_guard_strength')">
                        <a href="{{URL::route('reduce_guard_strength')}}">
                            <i class="fa fa-users"></i>
                            <span>Reduce Guard Strength</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('ansar_before_reduce_view')">
                        <a href="{{URL::route('ansar_before_reduce_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar List Before Reduction</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('kpi-withdraw-view')">
                        <a href="{{URL::route('kpi-withdraw-view')}}">
                            <i class="fa fa-users"></i>
                            <span>Withdraw KPI</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('withdrawn_kpi_list')">
                        <a href="{{URL::route('withdrawn_kpi_view')}}">
                            <i class="fa fa-users"></i>
                            <span>KPI Withdrawal Date Update</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('inactive_kpi_list')">
                        <a href="{{URL::route('inactive_kpi_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Inactive KPI List</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('kpi_withdraw_cancel_view')">
                        <a href="{{URL::route('kpi_withdraw_cancel_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Cancel KPI Withdrawal</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Personal info</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('anser_list')">
                        <a href="{{URL::to('HRM/entrylist')}}">
                            <i class="fa fa-users"></i>
                            <span>Entry</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('chunk_verify')">
                        <a href="{{URL::to('HRM/chunkverify')}}">
                            <i class="fa fa-users"></i>
                            <span>Verify Entry(Chunk)</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('entry_draft')">
                        <a href="{{URL::to('HRM/entrydraft')}}">
                            <i class="fa fa-users"></i>
                            <span>Draft Entry</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('entry_advanced_search')">
                        <a href="{{URL::to('HRM/entryadvancedsearch')}}">
                            <i class="fa fa-users"></i>
                            <span>Advanced Search</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('orginal_info')">
                        <a href="{{URL::to('HRM/originalinfo')}}">
                            <i class="fa fa-users"></i>
                            <span>Orginal Info</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('print_card_id_view')">
                        <a href="{{URL::to('HRM/print_card_id_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Print ID Card</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Service</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    {{--<li>--}}
                    {{--<a href="#">--}}
                    {{--<i class="fa fa-users"></i>--}}
                    {{--<span>Bank Receipt</span>--}}
                    {{--</a>--}}
                    {{--</li>--}}
                    <li ng-if="checkMenu('view_panel_list')">
                        <a href="{{URL::route('view_panel_list')}}">
                            <i class="fa fa-users"></i>
                            <span>Panel</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('make_offer')">
                        <a href="{{URL::route('make_offer')}}">
                            <i class="fa fa-users"></i>
                            <span>Offer</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('offer_quota')">
                        <a href="{{URL::route('offer_quota')}}">
                            <i class="fa fa-users"></i>
                            <span>Offer Quota</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Embodiment</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('go_to_new_embodiment_page')">
                                <a href="{{URL::route('go_to_new_embodiment_page')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Embodiment Entry</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('go_to_new_disembodiment_page')">
                                <a href="{{URL::route('go_to_new_disembodiment_page')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Disembodiment</span>
                                </a>
                            </li>
                            <?php $user_type = Auth::user()->type;
                            if($user_type == 11 || $user_type == 33){
                            ?>
                            <li ng-if="checkMenu('service_extension_view')">
                                <a href="{{URL::route('service_extension_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Service Extension</span>
                                </a>
                            </li>
                            <?php
                            }
                            ?>
                            <li ng-if="checkMenu('disembodiment_date_correction_view')">
                                <a href="{{URL::route('disembodiment_date_correction_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Disembodiment Date Correction</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('embodiment_memorandum_id_correction_view')">
                                <a href="{{URL::route('embodiment_memorandum_id_correction_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Embodiment Mem. ID Correction</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-users"></i>
                                    <span>Freeze</span>
                                    <i class="fa fa-angle-right pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li ng-if="checkMenu('freeze_view')">
                                        <a href="{{URL::route('freeze_view')}}">
                                            <i class="fa fa-users"></i>
                                            <span>Freeze for Disciplinary Action</span>
                                        </a>
                                    </li>
                                    <li ng-if="checkMenu('freeze_list')">
                                        <a href="{{URL::route('freeze_list')}}">
                                            <i class="fa fa-users"></i>
                                            <span>After Result of Freezing</span>
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
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('blacklist_entry_view')">
                                <a href="{{URL::route('blacklist_entry_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blacklist</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('unblacklist_entry_view')">
                                <a href="{{URL::route('unblacklist_entry_view')}}">
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
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('blocklist_entry_view')">
                                <a href="{{URL::route('blocklist_entry_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blocklist</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('unblocklist_entry_view')">
                                <a href="{{URL::route('unblocklist_entry_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Remove Ansar from Blocklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li ng-if="checkMenu('transfer_process')">
                        <a href="{{URL::route('transfer_process')}}">
                            <i class="fa fa-users"></i>
                            <span>Transfer Ansars</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-bar-chart"></i>
                    <span>Report</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('guard_report')">
                        <a href="{{URL::route('guard_report')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar in Guard Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('transfer_ansar_history')">
                        <a href="{{URL::route('transfer_ansar_history')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar Transfer Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('view_ansar_service_record')">
                        <a href="{{URL::route('view_ansar_service_record')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar Service Record</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('ansar_service_report_view')">
                        <a href="{{URL::route('ansar_service_report_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar Service Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('embodiment_report_view')">
                        <a href="{{URL::route('embodiment_report_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Embodied Ansar Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('disembodiment_report_view')">
                        <a href="{{URL::route('disembodiment_report_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Disembodied Ansar Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('blocklist_view')">
                        <a href="{{URL::route('blocklist_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Blocklisted Ansar Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('blacklist_view')">
                        <a href="{{URL::route('blacklist_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Blacklisted Ansar Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('three_year_over_report_view')">
                        <a href="{{URL::route('three_year_over_report_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Three Years Over Service Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('service_record_unitwise_view')">
                        <a href="{{URL::route('service_record_unitwise_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar Service Record Unit Wise</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('transfer_letter_view')">
                        <a href="{{URL::route('transfer_letter_view')}}">
                            <i class="fa fa-envelope"></i>
                            <span>Transfer Letter</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('transfer_letter_view')">
                        <a href="{{URL::route('offer_report')}}">
                            <i class="fa fa-envelope"></i>
                            <span>Offer Report</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('embodiment_letter_view')">
                        <a href="{{URL::route('embodiment_letter_view')}}">
                            <i class="fa fa-envelope"></i>
                            <span>Embodiment Letter</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('disembodiment_letter_view')">
                        <a href="{{URL::route('disembodiment_letter_view')}}">
                            <i class="fa fa-envelope"></i>
                            <span>Disembodiment Letter</span>
                        </a>
                    </li>
                </ul>
            </li>
            @if((Auth::user()->type==11||Auth::user()->type==33))
            <li>
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>DG Forms</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('direct_offer')">
                        <a href="{{URL::to('HRM/direct_offer')}}">
                            <i class="fa fa-users"></i>
                            <span>Direct offer</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('direct_panel_view')">
                        <a href="{{URL::to('HRM/direct_panel_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Direct Panel</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('direct_panel_cancel_view')">
                        <a href="{{URL::to('HRM/direct_panel_cancel_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Direct Cancel Panel</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('direct_embodiment')">
                        <a href="{{URL::to('HRM/direct_embodiment')}}">
                            <i class="fa fa-users"></i>
                            <span>Direct Embodiment</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('direct_disembodiment')">
                        <a href="{{URL::to('HRM/direct_disembodiment')}}">
                            <i class="fa fa-users"></i>
                            <span>Direct Dis-embodiment</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('direct_transfer')">
                        <a href="{{URL::route('direct_transfer')}}">
                            <i class="fa fa-send"></i>
                            <span>Direct Transfer</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>Direct BlockList</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('dg_blocklist_entry_view')">
                                <a href="{{URL::route('dg_blocklist_entry_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blocklist</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('dg_unblocklist_entry_view')">
                                <a href="{{URL::route('dg_unblocklist_entry_view')}}">
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
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li ng-if="checkMenu('dg_blacklist_entry_view')">
                                <a href="{{URL::route('dg_blacklist_entry_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Add Ansar in Blacklist</span>
                                </a>
                            </li>
                            <li ng-if="checkMenu('dg_unblacklist_entry_view')">
                                <a href="{{URL::route('dg_unblacklist_entry_view')}}">
                                    <i class="fa fa-users"></i>
                                    <span>Remove Ansar from Blacklist</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif
            @if(Auth::user()->type==11)
            <li>
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>Admin</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{URL::to('HRM/global_parameter')}}">
                            <i class="fa fa-users"></i>
                            <span>Global Parameter</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('HRM/cancel_offer')}}">
                            <i class="fa fa-users"></i>
                            <span>Cancel Offer</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('HRM/print_id_list')}}">
                            <i class="fa fa-users"></i>
                            <span>Ansar id list</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('HRM/rejected_offer_list')}}">
                            <i class="fa fa-users"></i>
                            <span>Rejected offer list</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            <li>
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span>General Settings</span>
                    <i class="fa fa-angle-right pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li ng-if="checkMenu('view_session_list')">
                        <a href="{{URL::to('HRM/session_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Session Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('unit_view')">
                        <a href="{{URL::to('HRM/unit_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Unit Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('thana_view')">
                        <a href="{{URL::to('HRM/thana_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Thana Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('disease_view')">
                        <a href="{{URL::to('HRM/disease_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Disease Information</span>
                        </a>
                    </li>
                    <li ng-if="checkMenu('skill_view')">
                        <a href="{{URL::to('HRM/skill_view')}}">
                            <i class="fa fa-users"></i>
                            <span>Skill Information</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
    <script>
        $(document).ready(function(){
            var l = $('.sidebar-menu').children('li');
            function removeMenu(m){

                m.each(function () {
                    //alert($(this).children('ul').length+" "+$(this).children('ul').children('li').length)
                    if($(this).children('ul').length>0&&$(this).children('ul').children('li').length>0){
                      //  alert('nnnn')
                        removeMenu($(this).children('ul').children('li'));
                    }
                    else if($(this).children('ul').length>0&&$(this).children('ul').children('li').length<=0){
                       // alert(m.length)
                        $(this).remove();
                    }
                })
            }
            removeMenu(l)
        })
    </script>
</aside>
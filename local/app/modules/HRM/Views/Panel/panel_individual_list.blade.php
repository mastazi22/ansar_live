<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    @include('template.resource')
    <style>

        @media print {
            body * {
                visibility: hidden;
            }

            .ansar-list, .ansar-list * {
                visibility: visible;
            }

            #print, #print * {
                visibility: hidden;
            }
            #paginate, #paginate *{
                visibility: hidden;
            }
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#print").on('click', function (e) {
                e.preventDefault();
                window.print()
            })
            $("#search").on('click', function (e) {
                $("#search").children('i').removeClass('fa-search').addClass('fa-spinner fa-pulse')
                $.ajax("{{URL::route('panel_list',['sex'=>$sex,'designation'=>$designation])}}",{
                    method:'get',
                    data:{ansar_id:$("#ansar_id").val()},
                    success: function (response) {
                        console.log(response)
                        $("#ansar_id_search").html(response);
                        $("#ansar_id_searchh").text($("#ansar_id").val());
                        $("#search-modal").modal();
                        $("#search").children('i').addClass('fa-search').removeClass('fa-spinner fa-pulse')
                    },
                    error: function (response) {
                        $("#search").children('i').addClass('fa-search').removeClass('fa-spinner fa-pulse')
                    }
                })
            })
        })
    </script>
</head>
<body class="skin-blue" style="background: #ecf0f5">
<div class="header-top">
    <div class="logo">
        <a href="http://ansarerp.local"><img src="http://ansarerp.local/dist/img/erp-logo.png" class="img-responsive" alt=""></a>
    </div>
    <div class="middle_header_logo">
        <img src="http://ansarerp.local/dist/img/erp-hdeader.png" class="img-responsive" alt="" width="400" height="400">
    </div>
    <div class="clearfix"></div>

</div>
<div class="content-wrapper" style="margin: 0">
    <section class="content">
        <div class="box box-solid" id="print-div">
            <div class="box-header">
                <h3 style="text-align: center">{{App\modules\HRM\Models\Designation::find($designation)->name_bng.'('.((strcasecmp($sex, 'male') == 0) ? 'পুরুষ' : 'মহিলা').')'}}&nbsp;তালিকা({{count($ansarList)}})<a id="print" href="#" title="print" class=""><span
                                class="glyphicon glyphicon-print"></span></a></h3>
                <div class="row">
                    <div class="col-sm-3 col-centered">
                        <form ng-submit="searchId()" class="sidebar-form">
                            <div class="input-group">
                                <input type="text" name="q" autocomplete="off" class="form-control" ng-model="searchAnsarId" placeholder="Search by ansar id...">
                                <span class="input-group-btn">
                                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                                 </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>SL. No</th>
                            <th>Id</th>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Own District</th>
                            <th>Thana</th>
                            <th>Panel Date &amp; Time</th>
                            <th>Panel Id</th>
                        </tr>
                        <?php $i=1; ?>
                        @forelse($ansarList as $ansar)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$ansar->ansar_id}}</td>
                                <td>{{$ansar->rank}}</td>
                                <td>{{$ansar->ansar_name_bng}}</td>
                                <td>{{$ansar->unit_name_bng}}</td>
                                <td>{{$ansar->thana_name_bng}}</td>
                                <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$ansar->created_at)->format("d-M-Y  h:i:s A")}}</td>
                                <td>{{$ansar->memorandum_id}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="warning">
                                    No ansar found
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<footer class="main-footer" style="margin-left: 0 !important;">
    <div class="pull-right hidden-xs"><b>Developed by</b> <a href="#">shurjoMukhi</a></div>
    <strong>2015 © <a href="#">Ansar &amp; VDP</a></strong> All rights reserved.
</footer>
</body>
</html>
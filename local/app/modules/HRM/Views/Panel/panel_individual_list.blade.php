<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Ansar Panel List</title>
    <link rel="shortcut icon" href=" {{asset('dist/img/favicon.ico')}}">
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('dist/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css"/>
    <script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>
    <style>
        body {
            background: #d2d6de;
            margin: 0;
        }

        .ansar-list {
            margin: 3% auto;
            width: 90%;
            background-color: #FFFFFF;
            padding: 10px 15px 20px 15px;
        }

        .ansar-list h3 {
            margin-bottom: 20px;
            text-align: center;
        }

        .ansar-list h3 a {
            margin-left: 20px;
        }

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
                $.ajax("{{action('PanelController@getPanelListBySexAndDesignation',['sex'=>$sex,'designation'=>$designation])}}",{
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
<body>
<div class="ansar-list" id="print-div">
    <h3>{{App\models\Designation::find($designation)->name_bng.'('.((strcasecmp($sex, 'male') == 0) ? 'পুরুষ' : 'মহিলা').')'}}&nbsp;তালিকা({{count($ansarList)}})<a id="print" href="#" title="print" class=""><span
                    class="glyphicon glyphicon-print"></span></a></h3>
    <div style="width: 300px;margin: 10px auto">
        <div class="input-group">
            <input type="text" id="ansar_id" class="form-control" placeholder="Search by ansar id">
            <span class="input-group-btn">
                <button class="btn btn-default" id="search"><i class="fa fa-search"></i></button>
            </span>
        </div>
    </div>

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
            @if(count($ansarList)==0)
                <tr>
                    <td colspan="5" class="warning">
                        No ansar found
                    </td>
                </tr>
            @else
                <?php $i = 1; ?>
                @foreach($ansarList as $ansar)
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
                @endforeach
            @endif
        </table>
    </div>
    <div class="row" id="paginate">
        <div class="col-md-3 col-sm-3">
            <span>
                Show per page :
                <select>
                    <option>10</option>
                    <option>20</option>
                    <option>30</option>
                </select>
            </span>
        </div>
        <div class="col-md-9 col-sm-9">

        </div>
    </div>
</div>
<div id="search-modal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80%;margin: 20px auto">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Searched Ansar Id:<span id="ansar_id_searchh"></span></h3>
            </div>
            <div class="modal-body" id="ansar_id_search">

            </div>
        </div>
    </div>
</div>
</body>
</html>
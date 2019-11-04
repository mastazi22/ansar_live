@extends('template.master')
@section('title','Ansar Transfer History')
@section('breadcrumb')
    {!! Breadcrumbs::render('transfer_ansar_history') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope,$http) {
            $scope.ansars = [];
            $scope.allLoading = false;
            $scope.errorFound=0;
            $scope.loadTransferHistory = function (ansar_id) {
                $scope.allLoading = true;
                $http({
                    url:'{{URL::route('get_transfer_ansar_history')}}',
                    method:'get',
                    params:{ansar_id:ansar_id}
                }).then(function (response) {
                    $scope.errorFound=0;
                    $scope.ansars = response.data;
                    $scope.allLoading = false;
                },function (response) {
                    $scope.errorFound=1;
                    $scope.allLoading = false;
                    $scope.ansars = '';
                    $scope.errorMessage = "Please enter a valid Ansar ID";
                })
            }
            $scope.loadTransferHistoryOnKeyPress = function (ansar_id,$event) {
                if($event.keyCode==13) {
                    $scope.allLoading = true;
                    $http({
                        url: '{{URL::route('get_transfer_ansar_history')}}',
                        method: 'get',
                        params: {ansar_id: ansar_id}
                    }).then(function (response) {
                        $scope.errorFound=0;
                        $scope.ansars = response.data;
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.errorFound=1;
                        $scope.allLoading = false;
                        $scope.ansars = '';
                        $scope.errorMessage = "Please enter a valid Ansar ID";
                    })
                }
            }
            $scope.convertDate = function (d) {
                return moment(d).format("DD-MMM-YYYY")
            }
        })
        $(function () {
            function beforePrint(){
//                console.log($("body").find("#print-body").html())
                $("#print-area").remove();
                $('body').append('<div id="print-area" class="letter">'+$("#ansar_transfer_history").html()+'</div>')
            }
            function afterPrint(){
                $("#print-area").remove()
            }
            if(window.matchMedia){
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        beforePrint();
                    } else {
                        afterPrint();
                    }
                });
            }
            window.onbeforeprint = beforePrint;
            window.onafterprint = afterPrint;
            $('body').on('click','#print-report', function (e) {
               // alert("pppp")
                e.preventDefault();

                window.print();

            })
        })

    </script>
    <style>
        input::-webkit-input-placeholder {
            color: #7b7b7b !important;
        }

        input:-moz-placeholder { /* Firefox 18- */
            color: #7b7b7b !important;
        }

        input::-moz-placeholder {  /* Firefox 19+ */
            color: #7b7b7b !important;
        }

        input:-ms-input-placeholder {
            color: #7b7b7b !important;
        }
    </style>
    <div ng-controller="TransferController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body"><br>
                    <div class="row">
                        <div class="col-md-6 col-centered">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{--<label class="control-label">Enter a ansar id</label>--}}
                                    <input type="text" ng-model="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-keypress="loadTransferHistoryOnKeyPress(ansar_id,$event)">
                                    <span class="text-danger" ng-if="errorFound==1"><p>[[errorMessage]]</p></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" ng-click="loadTransferHistory(ansar_id)">Generate Transfer Report</button>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="ansar_transfer_history">
                            <h3 style="text-align: center">Ansar Transfer History&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>SL. No</th>
                                        <th>From KPI</th>
                                        <th>To KPI</th>
                                        <th>District</th>
                                        <th>Thana</th>
                                        <th>Embodiment Date</th>
                                        <th>Transfer Date</th>
                                    </tr>
                                    <tr ng-show="ansars.length==0">
                                        <td colspan="7" class="warning">
                                            No Ansar is available to show
                                        </td>
                                    </tr>
                                    <tbody ng-if="errorFound==1" ng-bind-html="ansars"></tbody>
                                    <tr ng-repeat="a in ansars" ng-show="ansars.length>0">
                                        <td>[[$index+1]]</td>
                                        <td>[[a.FromkpiName]]</td>
                                        <td>[[a.TokpiName]]</td>
                                        <td>[[a.unit]]</td>
                                        <td>[[a.thana]]</td>
                                        <td>[[convertDate(a.joiningDate)]]</td>
                                        <td>[[convertDate(a.transferDate)]]</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
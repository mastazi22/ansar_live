@extends('template.master')
@section('title','View Service Record')
@section('breadcrumb')
    {!! Breadcrumbs::render('view_ansar_service_record') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope,$http,$sce) {
            $scope.ansarDetail = {};
            $scope.allLoading = false;
            $scope.exist = false;
            $scope.errorFound=0;
            $scope.loadAnsarDetail = function (id) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('ansar_detail_info')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.errorFound=0;
                    $scope.ansarDetail = response.data
                    //$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                    $scope.allLoading = false;
                },function(response){
                    $scope.ansarDetail = '';
                    $scope.errorFound = 1;
                    $scope.errorMessage = "Please enter a valid Ansar ID";
                    $scope.allLoading = false;
//                    $scope.ansarDetail = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                })
            }
            $scope.loadAnsarDetailOnKeyPress = function (ansar_id,$event) {
                if($event.keyCode==13) {
                    $scope.allLoading = true;
                    $http({
                        method: 'get',
                        url: '{{URL::route('ansar_detail_info')}}',
                        params: {ansar_id: ansar_id}
                    }).then(function (response) {
                        $scope.errorFound=0;
                        $scope.ansarDetail = response.data
                        //if($scope.ansarDetail.apid)$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                        $scope.allLoading = false;
                    }, function (response) {
                        $scope.ansarDetail = '';
                        $scope.errorFound = 1;
                        $scope.errorMessage = "Please enter a valid Ansar ID";
                        $scope.allLoading = false;
//                        $scope.ansarDetail = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    })
                }
            }
            {{--$scope.checkFile = function(url){--}}
                {{--$http({--}}
                    {{--url:'{{URL::to('/check_file')}}',--}}
                    {{--params:{path:url},--}}
                    {{--method:'get'--}}
                {{--}).then(function (response) {--}}
                    {{--$scope.exist = response.data.status;--}}
                {{--}, function () {--}}
                    {{--$scope.exist = false;--}}
                {{--})--}}
            {{--}--}}
            $scope.dateConvert=function(date){
                return (moment(date).locale('bn').format('DD-MMMM-YYYY'));
            }
        })
        $(function () {
            $('body').on('click','#print-report', function (e) {
                //alert("pppp")
                e.preventDefault();
                $("#print-area").remove();
//                console.log($("body").find("#print-body").html())
                $('body').append('<div id="print-area">'+$("#ansar_service_record").html()+'</div>')
               // beforePrint()
                window.print();
                $("#print-area").remove()
               // afterPrint()
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
                                    <input type="text" ng-model="ansar_id" class="form-control" placeholder="Enter Ansar ID" ng-keypress="loadAnsarDetailOnKeyPress(ansar_id,$event)">
                                    <span class="text-danger" ng-if="errorFound==1"><p>[[errorMessage]]</p></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" ng-click="loadAnsarDetail(ansar_id)">Generate Ansar Service Record</button>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="ansar_service_record">
                            <h3 style="text-align: center">View Ansar Service Record&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>

                            <div ng-if="!ansarDetail.apid||errorFound==1">
                                <h4 style="text-align: center">No Ansar is available to show</h4>
                            </div>
                            <div ng-if="ansarDetail.apid">
                                <div class="form-group">
                                    <div class="col-sm-5 col-sm-offset-3 center-profile">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td rowspan="5"  style="vertical-align: middle;width: 130px;height: 150px;background: #ffffff">
                                                        <img  style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[ansarDetail.apid.profile_pic]]" alt="">
                                                    </td>
                                                    <th style="background: #ffffff">ID</th>
                                                    <td style="background: #ffffff">[[ansarDetail.apid.ansar_id]]</td>
                                                </tr>
                                                <tr>

                                                    <th style="background: #ffffff">Name</th>
                                                    <td style="background: #ffffff">[[ansarDetail.apid.ansar_name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th style="background: #ffffff">Rank</th>
                                                    <td style="background: #ffffff">[[ansarDetail.apid.name_bng]]</td>
                                                </tr>
                                                <tr>
                                                    <th style="background: #ffffff">Mobile No.</th>
                                                    <td style="background: #ffffff">[[ansarDetail.apid.mobile_no_self]]</td>
                                                </tr>
                                                <tr>
                                                    <th style="background: #ffffff">Home District</th>
                                                    <td style="background: #ffffff">[[ansarDetail.apid.unit_name_bng]]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <caption>পানেল্ভুক্তির ও অফারের বিবরণ</caption>
                                                <tr>
                                                    <td>পানেল্ভুক্তির তারিখ</td>
                                                    <td>প্যানেল আইডি নং</td>
                                                    <td>বর্তমান অবস্থা</td>
                                                    <td>অফারের তারিখ</td>
                                                    <td>অফারের জেলা</td>
                                                    <td>অফার বাতিলের তারিখ</td>
                                                </tr>
                                                <tr>
                                                    <td>[[ansarDetail.api.panel_date?dateConvert(ansarDetail.api.panel_date):"--"]]</td>
                                                    <td>[[ansarDetail.api.memorandum_id?ansarDetail.api.memorandum_id:"--"]]</td>
                                                    <td>[[ansarDetail.status]]</td>
                                                    <td>[[ansarDetail.aod.offerDate?dateConvert(ansarDetail.aod.offerDate):'--']]</td>
                                                    <td>[[ansarDetail.aod.offerUnit?ansarDetail.aod.offerUnit:'--']]</td>
                                                    <td>[[ansarDetail.aoci.offerCancel?dateConvert(ansarDetail.aoci.offerCancel):'--']]</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td style="background: #ffffff">
                                                        <table class="table table-bordered">
                                                            <caption>অঙ্গিভুতির বিবরণ</caption>
                                                            <tr>
                                                                <td>অঙ্গিভুতির  তারিখ</td>
                                                                <td>অঙ্গিভুতির আইডি নং</td>
                                                                <td>জেলার নাম</td>
                                                                <td>অঙ্গিভুতির সংস্থা</td>
                                                            </tr>
                                                            <tr>
                                                                <td>[[ansarDetail.aei.joining_date?dateConvert(ansarDetail.aei.joining_date):"--"]]</td>
                                                                <td>[[ansarDetail.aei.memorandum_id?ansarDetail.aei.memorandum_id:"--"]]</td>
                                                                <td>[[ansarDetail.aei.unit_name_bng?ansarDetail.aei.unit_name_bng:"--"]]</td>
                                                                <td>[[ansarDetail.aei.kpi_name?ansarDetail.aei.kpi_name:"--"]]</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="background: #ffffff">
                                                        <table class="table table-bordered">
                                                            <caption>অঙ্গিভুতির বিবরণ</caption>
                                                            <tr>
                                                                <td>অ-অঙ্গিভুতির  তারিখ</td>
                                                                <td>অ-অঙ্গিভুতির কারন</td>
                                                            </tr>
                                                            <tr>
                                                                <td>[[ansarDetail.adei.disembodiedDate?dateConvert(ansarDetail.adei.disembodiedDate):"--"]]</td>
                                                                <td>[[ansarDetail.adei.disembodiedReason?ansarDetail.adei.disembodiedReason:"--"]]</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
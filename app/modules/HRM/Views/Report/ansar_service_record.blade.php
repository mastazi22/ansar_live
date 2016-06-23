@extends('template.master')
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope,$http) {
            $scope.ansarDetail = {};
            $scope.isLoading = false;
            $scope.exist = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.isLoading = true;
                $http({
                    method: 'get',
                    url: '{{action('DGController@loadAnsarDetail')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data
                    //$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                    $scope.isLoading = false;
                    console.log(response.data)
                })
            }
            $scope.loadAnsarDetailOnKeyPress = function (ansar_id,$event) {
                if($event.keyCode==13) {
                    $scope.isLoading = true;
                    $http({
                        method: 'get',
                        url: '{{action('DGController@loadAnsarDetail')}}',
                        params: {ansar_id: ansar_id}
                    }).then(function (response) {
                        $scope.ansarDetail = response.data
                        //if($scope.ansarDetail.apid)$scope.checkFile($scope.ansarDetail.apid.profile_pic)
                        $scope.isLoading = false;
                        console.log(response.data)
                    }, function (response) {
                        $scope.isLoading = false;
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
                return (moment(date).locale('bn').format('DD-MMM-YYYY'));
            }
        })
        $(function () {
            $('body').on('click','#print-report', function (e) {
                //alert("pppp")
                e.preventDefault();
               // beforePrint()
                window.print();
               // afterPrint()
            })
            var beforePrint = function() {
                if($("#print-area").length<=0)$('body').append('<div id="print-area" class="letter">'+$("#ansar_service_record").html()+'</div>')
                console.log('Functionality to run before printing.');
            };
            var afterPrint = function() {
                $("#print-area").remove()
                console.log('Functionality to run after printing');
            };

            if (window.matchMedia) {
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
        })
    </script>
    <div class="content-wrapper" ng-controller="TransferController">
        <div class="loading-report animated" ng-class="{fadeInDown:isLoading,fadeOutUp:!isLoading}">
            <img src="{{asset('dist/img/ring-alt.gif')}}" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a>Ansar Service Record</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Enter Ansar Id
                                        </label>
                                        <input type="text" ng-model="ansar_id" class="form-control" placeholder="Enter Ansar Id" ng-keypress="loadAnsarDetailOnKeyPress(ansar_id,$event)">

                                    </div>
                                    <button class="btn btn-primary" ng-click="loadAnsarDetail(ansar_id)">Generate Ansar Service Report</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12" id="ansar_service_record">
                                    <h3 style="text-align: center">Ansar Service Record&nbsp;<a href="#" id="print-report"><span class="glyphicon glyphicon-print"></span></a></h3>

                                    <div ng-if="!ansarDetail.apid">
                                        <h3 style="text-align: center">No Ansar Found</h3>
                                    </div>
                                    <div ng-if="ansarDetail.apid">
                                        <div class="form-group">
                                            <div class="col-sm-5 col-sm-offset-3 center-profile">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <td rowspan="5"  style="vertical-align: middle;width: 130px;height: 150px">
                                                                <img  style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[ansarDetail.apid.profile_pic]]" alt="">
                                                            </td>
                                                            <th>Id</th>
                                                            <td>[[ansarDetail.apid.ansar_id]]</td>
                                                        </tr>
                                                        <tr>

                                                            <th>Name</th>
                                                            <td>[[ansarDetail.apid.ansar_name_bng]]</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Rank</th>
                                                            <td>[[ansarDetail.apid.name_bng]]</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Mobile No.</th>
                                                            <td>[[ansarDetail.apid.mobile_no_self]]</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Home District</th>
                                                            <td>[[ansarDetail.apid.unit_name_bng]]</td>
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
                                                            <td ng-if="1==ansarDetail.asi.block_list_status">Blocked</td>
                                                            <td ng-if="0==ansarDetail.asi.block_list_status">
                                                                <span ng-if="1==ansarDetail.asi.free_status">Free</span>
                                                                <span ng-if="1==ansarDetail.asi.pannel_status">Panel</span>
                                                                <span ng-if="1==ansarDetail.asi.offer_sms_status">Offered</span>
                                                                <span ng-if="1==ansarDetail.asi.embodied_status">Embodied</span>
                                                                <span ng-if="1==ansarDetail.asi.freezing_status">Freeze</span>
                                                                <span ng-if="1==ansarDetail.asi.early_retierment_statBlockedus">Early retirement</span>
                                                                <span ng-if="1==ansarDetail.asi.block_list_status"></span>
                                                                <span ng-if="1==ansarDetail.asi.black_list_status">Blacked</span>
                                                                <span ng-if="1==ansarDetail.asi.rest_status">Rest</span>
                                                                <span ng-if="1==ansarDetail.asi.retierment_status">Retirement</span>
                                                            </td>
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
                                                            <td>
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
                                                            <td>
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
                </div>
            </div>
        </section>
    </div>
@stop
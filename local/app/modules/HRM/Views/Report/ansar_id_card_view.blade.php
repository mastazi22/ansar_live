@extends('template.master')
@section('title','Print ID Card')
@section('breadcrumb')
    {!! Breadcrumbs::render('print_card_id_view') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('printIdController', function ($scope, $http, $sce) {
            $scope.isLoading = false;
            $scope.reportType = 'eng';
            $scope.ansarId = ""
            $scope.errors = ''
            $scope.id = moment().format("DD-MMM-YYYY");
            $scope.ed = moment().add(10, 'years').subtract(1, 'days').format("DD-MMM-YYYY");
            $scope.isLoading = false;
            $scope.idCard = $sce.trustAsHtml("");
            $scope.generateIdCard = function () {
//                var id = new Date($scope.id);
//                var ed = new Date($scope.ed);
                // alert(id.getDate()+'-'+((id.getMonth()+1)<10?'0'+(id.getMonth()+1):(id.getMonth()+1))+'-'+id.getFullYear())
                $scope.isLoading = true;
                $http({
                    url: '{{URL::to('HRM/id_card_history')}}',
                    method: 'get',
                    params: {
                        ansar_id: $scope.ansarId
                    }
                }).then(function (response) {
                    $scope.isLoading = false;
                    console.log(response.data);
                    if (response.data.validation != undefined && response.data.validation == true) {
                        $scope.errors = response.data.messages;
                    }
                    else {
                        $scope.errors = ''
                        $scope.idCard = response.data;
                        $scope.isLoading = false;
                    }
                })
            }

        })
        $(document).ready(function () {
            $(window).keypress(function (event) {
                var key = event.which||event.keyCode;
                if(key==13) {
                    event.preventDefault();
                    return false;
                }
            })
        })
    </script>
    <div ng-controller="printIdController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('print_card_id_view') !!}--}}
        {{--</div>--}}
        <section class="content">

            <div class="box box-solid">
                <div class="overlay" ng-if="isLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <form action="{{URL::route('print_card_id')}}" method="post" target="_blank">
                        {!! csrf_field() !!}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Enter Ansar ID</label>
                                <input type="text" class="form-control" name="ansar_id" ng-model="ansarId"
                                       placeholder="Ansar ID">

                                <p class="text text-danger" ng-if="errors.ansar_id!=undefined">[[errors.ansar_id[0]
                                    ]]</p>
                            </div>
                            <div class="form-group">
                                <Label class="control-label">Issue Date</Label>
                                <input type="text"  id="issue_date" class="form-control" name="issue_date"
                                       ng-model="id">

                                <p class="text text-danger" ng-if="errors.issue_date!=undefined">[[errors.issue_date[0]
                                    ]]</p>
                            </div>
                            <div class="form-group">
                                <Label class="control-label">Expire Date</Label>
                                <input type="text"  id="expire_date" class="form-control" name="expire_date"
                                       ng-model="ed">

                                <p class="text text-danger" ng-if="errors.expire_date!=undefined">
                                    [[errors.expire_date[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <Label class="control-label">View ID Card in</Label>
                                        <span class="control-label" style="padding: 5px 8px">
                                            <input type="radio" name="type" class="radio-inline" style="margin: 0 !important;"
                                                   value="eng" ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio"  name="type" class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                            </div>
                            <div class="form-group">
                                <a ng-click="generateIdCard()" class="btn btn-info">Generate ID Card</a>
                            </div>
                        </div>

                        <div class="col-sm-6 col-sm-offset-1" style="z-index: 5" >
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped">
                                    <tr>
                                        <th>#</th>
                                        <th>Card Type</th>
                                        <th>Rank</th>
                                        <th>Issue Date</th>
                                        <th>Expire Date</th>
                                    </tr>
                                    <tr ng-repeat="id in idCard">
                                        <td>[[$index+1]]</td>
                                        <td>[[id.type]]</td>
                                        <td>[[id.rank]]</td>
                                        <td>[[id.issue_date]]</td>
                                        <td>[[id.expire_date]]</td>
                                    </tr>
                                </table>
                            </div>
                            <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print ID Card</button>

                        </div>


                    </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@stop
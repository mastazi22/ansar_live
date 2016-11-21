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
                    url: '{{URL::to('HRM/print_card_id')}}',
                    method: 'get',
                    params: {
                        ansar_id: $scope.ansarId,
                        type: $scope.reportType,
                        issue_date: $scope.id,
                        expire_date: $scope.ed
                    }
                }).then(function (response) {
                    $scope.isLoading = false;
                    console.log(response.data);
                    if (response.data.validation != undefined && response.data.validation == true) {
                        $scope.errors = response.data.messages;
                    }
                    else {
                        $scope.errors = ''
                        $scope.idCard = $sce.trustAsHtml(response.data);
                        $scope.isLoading = false;
                        window.onbeforeunload = "Are you sure to leave this page before print id card."
                    }
                })
            }

        })
        $(function () {
            $('body').on('click', '#print-report', function (e) {
                alert("pppp")
                e.preventDefault();
                $('body').append('<div id="print-area" class="letter">' + $("#ansar_id_card").html() + '</div>')
                window.print();
                $("#print-area").remove()
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
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Enter Ansar ID</label>
                                <input type="text" class="form-control" ng-model="ansarId"
                                       placeholder="Ansar ID">

                                <p class="text text-danger" ng-if="errors.ansar_id!=undefined">[[errors.ansar_id[0]
                                    ]]</p>
                            </div>
                            <div class="form-group">
                                <Label class="control-label">Issue Date</Label>
                                <input type="text" disabled id="issue_date" class="form-control" name="issue_date"
                                       ng-model="id">

                                <p class="text text-danger" ng-if="errors.issue_date!=undefined">[[errors.issue_date[0]
                                    ]]</p>
                            </div>
                            <div class="form-group">
                                <Label class="control-label">Expire Date</Label>
                                <input type="text" disabled id="expire_date" class="form-control" name="expire_date"
                                       ng-model="ed">

                                <p class="text text-danger" ng-if="errors.expire_date!=undefined">
                                    [[errors.expire_date[0] ]]</p>
                            </div>
                            <div class="form-group">
                                <Label class="control-label">View ID Card in</Label>
                                        <span class="control-label" style="padding: 5px 8px">
                                            <input type="radio" class="radio-inline" style="margin: 0 !important;"
                                                   value="eng" ng-model="reportType">&nbsp;<b>English</b>
                                &nbsp;<input type="radio" class="radio-inline" style="margin: 0 !important;" value="bng"
                                             ng-model="reportType">&nbsp;<b>বাংলা</b>
                            </span>
                            </div>
                            <div class="form-group">
                                <button ng-click="generateIdCard()" class="btn btn-info">Generate ID Card</button>
                            </div>
                        </div>

                        <div class="col-sm-6 col-sm-offset-1" style="z-index: 5" >
                            <h3 style="margin-top: 0;">
                                <a href="#" id="print-report">
                                    <i class="glyphicon glyphicon-print"></i>
                                </a>
                            </h3>
                            <div id="ansar_id_card">
                                <div ng-bind-html="idCard"></div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
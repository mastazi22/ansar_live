{{--User: Shreya--}}
{{--Date: 11/05/2015--}}
{{--Time: 11:00 AM--}}

@extends('template.master')
@section('title','Disembodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('disembodiment_entry') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#disembodiment_date').datePicker({
                defaultValue:moment()
            });
        })
        GlobalApp.controller('NewDisembodimentController', function ($scope, $http,notificationService) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}');
            $scope.queue = [];
            $scope.districts = [];
            $scope.formData = [];
            $scope.submitData = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.guards = [];
            $scope.guardDetail = [];
            $scope.ansars = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            $scope.disembodiment_date = moment().format("DD-MMM-YYYY");
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('load_ansar')}}',
                    method: 'get',
                    params: $scope.param
                }).then(function (response) {
                    console.log(response.data)
                    $scope.queue.shift();
                    if($scope.queue.length>1) $scope.loadAnsar();
                    $scope.allLoading = false;
                    if(response.data.status===false){
                        notificationService.notify('error',response.data.message);
                    }
                    $scope.ansars = response.data;
                })
            }
            $scope.verifyMemorandumId = function () {
                var data = {
                    memorandum_id: $scope.memorandumId
                }
                $scope.isVerified = false;
                $scope.isVerifying = true;
                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {
//                    alert(response.data.status)
                    $scope.isVerified = response.data.status;
                    $scope.isVerifying = false;
                }, function (response) {

                })
            }
            $scope.showFormData = function () {
                $scope.allLoading = true;
                $scope.submitData = [];
                for(var i=0;i<$scope.formData.length;i++){
                    if($scope.formData[i]==undefined){
                        continue;
                    }
                    if($scope.formData[i].disReason==undefined||!$scope.formData[i].disReason||$scope.formData[i].ansarId==undefined||!$scope.formData[i].ansarId){
                        continue;
                    }
                    $scope.submitData.push($scope.formData[i])
                }
                $scope.printLetter = false;
                $http({
                    url:'{{URL::to('HRM/disembodiment-entry')}}',
                    method:'post',
                    data:angular.toJson({
                        ansars:$scope.submitData,
                        memorandum_id:$scope.memorandumId,
                        mem_date:$scope.memDate,
                        disembodiment_comment:$scope.disembodiment_comment,
                        disembodiment_date:$scope.disembodiment_date,
                    })
                }).then(function (response) {
                    if(response.data.status){
                        notificationService.notify('success',response.data.message);
                        $scope.loadAnsar();
                        $scope.ch =[]
                        $scope.formData = [];
                        $scope.printLetter = true;
                    }
                    else{
                        notificationService.notify('error',response.data.message);
                    }
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error','An unknown error occur. Error code : '+response.status);
                })
            }
        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        //alert("hh")
                        scope.memorandumId = "";
                        //scope.disembodiment_date = "";
                        scope.disembodiment_comment = "";
                        scope.$digest();
                        $("#disembodiment-option").modal("toggle")
//                        $("#withrdaw-option").on('show.bs.modal', function () {
//                            alert("hh")
//                            scope.memorandumId = "";
//                            scope.kpi_withdraw_reason = "";
//                            scope.kpi_withdraw_date = "";
//                        })
//                        $("#withrdaw-option").on('hide.bs.modal', function () {
//                            //modalOpen = false;
//                        })
                    })
                }
            }
        })
    </script>
    <div ng-controller="NewDisembodimentController">
        {{--<div class="breadcrumbplace">--}}
        {{--{!! Breadcrumbs::render('disembodiment_entry') !!}--}}
        {{--</div>--}}
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana','kpi']"
                            type="single"
                            data="param"
                            start-load="range"
                            kpi-change="loadAnsar()"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                    >

                    </filter-template>
                    <div class="row">
                        <div class="col-sm-8">
                            <h4>Total Ansars&nbsp;[[ansars.ansar_infos.length]]</h4>
                        </div>
                        <div class="col-sm-4">
                            <database-search q="param.q" queue="queue" on-change="loadAnsar()"></database-search>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">

                            <tr>
                                <th>#</th>
                                <th>Ansar ID</th>
                                <th>Ansar Name</th>
                                <th>Ansar Unit</th>
                                <th>Ansar Thana</th>
                                <th>Designation</th>
                                <th>KPI Name</th>
                                <th>Reason of Disembodiment</th>
                                <th>Select From Here</th>
                            </tr>
                            <tr ng-repeat="a in ansars.ansar_infos">
                                <td>[[$index+1]]</td>
                                <td>[[a.ansar_id]]</td>
                                <td>[[a.ansar_name_bng]]</td>
                                <td>[[a.unit_name_bng]]</td>
                                <td>[[a.thana_name_bng]]</td>
                                <td>[[a.name_bng]]</td>
                                <td>[[a.kpi_name]]</td>
                                <td>
                                    <select name="dis-reason" ng-model="formData[$index].disReason" ng-change="!formData[$index].disReason?formData[$index].ansarId=ch[$index]=false:''" class="form-control dis-reason">
                                        <option value="">--Select Reason--</option>
                                        <option ng-repeat="r in ansars.reasons" value="[[r.id]]">[[r.reason_in_bng]]
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <div class="styled-checkbox">
                                        <input  type="checkbox"  id="a_[[a.ansar_id]]" ng-change="formData[$index].ansarId=ch[$index]" ng-disabled="!formData[$index].disReason" ng-model="ch[$index]" class="ansar-check" ng-true-value="[[a.ansar_id]]">
                                        <label for="a_[[a.ansar_id]]"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr colspan="10" class="warning" ng-if="ansars.ansar_infos==undefined||ansars.ansar_infos.length<=0">
                                <td colspan="10">No Ansar Found to show</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <h5 class="text text-bold">Dis Embodiment Options</h5>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Memorandum no. & Date</label>

                                <div class="row">
                                    <div class="col-md-6" style="padding-right: 0">
                                        <input
                                                ng-model="memorandumId"
                                                type="text" class="form-control"
                                                name="memorandum_id"
                                                placeholder="Enter Memorandum no."
                                                required>
                                    </div>
                                    <div class="col-md-6">
                                        <input date-picker ng-model="memDate"
                                               type="text" class="form-control" name="mem_date"
                                               placeholder="Memorandum Date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Date&nbsp;&nbsp;&nbsp;<span
                                            class="text-danger"
                                            ng-if="newDisembodimentForm.disembodiment_date.$touched && newDisembodimentForm.disembodiment_date.$error.required">Date is required.</span>
                                </label>

                                {!! Form::text('disembodiment_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'disembodiment_date',  'ng-model'=> 'disembodiment_date', 'disabled')) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Comment
                                    &nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                            ng-if="newDisembodimentForm.disembodiment_comment.$touched && newDisembodimentForm.disembodiment_comment.$error.required">Comment is required.</span></label>

                                {!! Form::text('disembodiment_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'disembodiment_comment', 'ng-model'=> 'disembodiment_comment', 'placeholder'=> 'Write Comment')) !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <a ng-if="printLetter" target="_blank" href="{{URL::to('HRM/print_letter')}}?id=[[memorandumId]]&unit=[[param.unit]]&view=full&type=DISEMBODIMENT" class="btn btn-primary pull-left">Print Embodiment Letter</a>
                    <button class="pull-right btn btn-primary" ng-disabled="allLoading" id="disembodiment-confirmation" ng-click="showFormData()">
                        <i class="fa fa-send"></i>&nbsp;&nbsp;Disembodied
                    </button>
                </div>
            </div>

        </section>
    </div>

@endsection
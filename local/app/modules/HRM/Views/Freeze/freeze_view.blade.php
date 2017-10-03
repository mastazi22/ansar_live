{{--User: Shreya--}}
{{--Date: 2/22/2016--}}
{{--Time: 2:17 PM--}}

@extends('template.master')
@section('title','Freeze for Disciplinary Action')
@section('breadcrumb')
    {!! Breadcrumbs::render('freeze') !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $('#freeze_date').datepicker({                dateFormat:'dd-M-yy'            })();
        })
        GlobalApp.controller('FreezeController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength = 0;
            $scope.loadingAnsar = false;

            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_for_freeze')}}',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.error = false;
                    $scope.ansarDetail = response.data
                    console.log($scope.ansarDetail)
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                }, function (response) {
                    $scope.error = true
                    $scope.loadingAnsar = false;
                })
            }
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            }
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                }
                else {
                    if (!$scope.ansarId)$scope.ansarDetail = {}
                }
            })
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
            $scope.verifyDate = function (i, j) {
                if (moment(i).isValid() || moment(j).isValid()) {
                    $cd = moment(i).format('DD-MMM-YYYY');
                    return moment(j).isSameOrBefore($cd)
                }
                else return false;
            }
            $scope.convertDate = function (d) {
                return moment(d).format('DD-MMM-YYYY')
            }
        })
    </script>

    <div ng-controller="FreezeController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="fa fa-remove"></span> {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;">
            <div class="box box-solid">
                {!! Form::open(array('route' => 'freeze_entry', 'id' => 'freeze_entry')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ansar_id" class="control-label">Ansar ID to Freeze</label>
                                <input type="text" name="ansar_id" id="ansar_id" class="form-control"
                                       placeholder="Enter Ansar ID" ng-model="ansarId" ng-change="makeQueue(ansarId)">
                                {!! $errors->first('ansar_id','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="memorandum_id" class="control-label">Memorandum no.</label>
                                <input ng-model="memorandumId" type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum no.">
                                {!! $errors->first('memorandum_id','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="freeze_date" class="control-label">Freeze Date</label>
                                <input type="text" name="freeze_date" id="freeze_date" class="form-control"
                                       ng-model="freeze_date">
                                {!! $errors->first('freeze_date','<p class="text text-danger">:message</p>') !!}
                            </div>
                            <div class="form-group">
                                <label for="freeze_comment" class="control-label">Comment for Freezing the Ansar</label>
                                {!! Form::textarea('freeze_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'freeze_comment', 'size' => '30x4', 'placeholder' => "Write any Comment", 'ng-model' => 'freeze_comment')) !!}
                            </div>
                            <button id="confirm-freeze" type="submit" class="btn btn-primary">Freeze</button>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar"></div>
                            <div ng-if="ansarDetail.name==undefined&&!error">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.name!=undefined&&!error">
                                <div class="form-group">
                                    <label class="control-label">Name</label>

                                    <p>
                                        [[ansarDetail.name]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>

                                    <p>
                                        [[ansarDetail.rank]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Name</label>

                                    <p>
                                        [[ansarDetail.kpi]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Unit</label>

                                    <p>
                                        [[ansarDetail.unit]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">KPI Thana</label>

                                    <p>
                                        [[ansarDetail.thana]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Sex</label>

                                    <p>
                                        [[ansarDetail.sex]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>

                                    <p>
                                        [[convertDate(ansarDetail.dob)]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label status-check">Reporting Date</label>

                                    <p>
                                        [[convertDate(ansarDetail.r_date)]]
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label status-check">Joining Date</label>

                                    <p>
                                        [[convertDate(ansarDetail.j_date)]]
                                    </p>
                                </div>
                            </div>
                            <div ng-if="error">
                                An Server Occur. Contact with system administrator
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </section>
    </div>
    <script>
        $("#confirm-freeze").confirmDialog({
            message: 'Are you sure to Freeze this Ansar',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#freeze_entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script>
@endsection

{{--User: Shreya--}}
{{--Date: 11/05/2015--}}
{{--Time: 11:00 AM--}}

@extends('template.master')
@section('title','Disembodiment')
@section('breadcrumb')
    {!! Breadcrumbs::render('embodiment_entry') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('NewDisembodimentController', function ($scope, $http) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.districts = [];
            $scope.thanas = [];
            $scope.selectedDistrict = "";
            $scope.selectedThana = "";
            $scope.selectedKpi = "";
            $scope.guards = [];
            $scope.guardDetail = [];
            $scope.ansars = [];
            $scope.loadingDistrict = true;
            $scope.loadingThana = false;
            $scope.loadingKpi=false;
            $scope.memorandumId = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
            $scope.allLoading = false;
            $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
            $scope.loadDistrict = function () {
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/DistrictName')}}'
                }).then(function (response) {
                    $scope.districts = response.data;
                    $scope.loadingDistrict = false;
                })
            }
            $scope.loadThana = function (id) {
                $http({
                    method: 'get',
                    url: '{{URL::to('HRM/ThanaName')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.thanas = response.data;
                    $scope.selectedThana = "";
                    $scope.loadingThana = true;
                })
            }
            $scope.loadGuard = function (id) {
                $http({
                    method: 'get',
                    url: '{{URL::route('kpi_name')}}',
                    params: {id: id}
                }).then(function (response) {
                    $scope.loadingThana = false;
                    $scope.guards = response.data;
                    $scope.selectedKpi = "";
                    //$scope.loadingKpi=true;
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
            if ($scope.isAdmin == 11) {
                $scope.loadDistrict()
            }
            else {
                if (!isNaN($scope.dcDistrict)) {
                    $scope.loadThana($scope.dcDistrict)
                    console.log($scope.dcDistrict)
                }
            }

        })
    </script>
    <div class="content-wrapper" style="min-height: 490px" ng-controller="NewDisembodimentController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <div id="all-loading"
             style="position:absolute;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.27);z-index: 100; display: none">
            <div style="position: relative;width: 20%;height: auto;margin: 20% auto;text-align: center;background: #FFFFFF">
                <img class="img-responsive" src="{{asset('dist/img/loading-data.gif')}}"
                     style="position: relative;margin: 0 auto">
                <h4>Loading....</h4>
            </div>

        </div>
        <section class="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3" ng-show="isAdmin==11">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a District<img ng-show="loadingDistrict" src="{{asset('dist/img/facebook.gif')}}"
                                                          width="16">
                                </label>
                                <select class="form-control" ng-model="selectedDistrict"
                                        ng-change="loadThana(selectedDistrict)">
                                    <option value="">--Select a District--</option>
                                    <option ng-repeat="d in districts" value="[[d.id]]">[[d.unit_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Thana<img ng-show="loadingThana" src="{{asset('dist/img/facebook.gif')}}"
                                                       width="16">
                                </label>
                                <select class="form-control" ng-model="selectedThana"
                                        ng-change="loadGuard(selectedThana)" name="thana_id">
                                    <option value="">--Select a Thana--</option>
                                    <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_eng]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">
                                    Select a Guard<img ng-show="loadingKpi" src="{{asset('dist/img/facebook.gif')}}"
                                                       width="16">
                                </label>
                                <select class="form-control" ng-model="selectedKPI" name="kpi_id">
                                    <option value="">--Select Guard--</option>
                                    <option value=0>All</option>
                                    <option ng-repeat="d in guards" value="[[d.id]]">[[d.kpi_name]]
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="float: right; margin-top: 25px">

                            <div class="form-group">
                                <label class="control-label">
                                </label>
                                <button id="load-ansar-for-disembodiment"
                                        class="pull-right btn btn-primary glyphicon glyphicon-save">&nbsp;Load Ansar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                            <tr class="info">
                                <th>Ansar ID</th>
                                <th>Ansar Name</th>
                                <th>Ansar Unit</th>
                                <th>Ansar Thana</th>
                                <th>Designation</th>
                                <th>KPI Name</th>
                                <th>Reason of Disembodiment</th>
                                <th>Select From Here</th>
                            </tr>
                            </tr>
                            <tbody id="ansar-all" class="status">
                            <tr colspan="10" class="warning" id="not-find-info">
                                <td colspan="10">No Ansar Found to show</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="pull-right btn btn-primary" data-toggle="modal" data-target="#disembodiment-option"
                            ng-click="modalOpen = true" id="disembodiment-confirmation" disabled>
                        <i class="fa fa-send"></i>&nbsp;&nbsp;Disembodied
                    </button>
                </div>
            </div>
            {!! Form::open(array('route' => 'disembodiment-entry', 'name' => 'newDisembodimentForm', 'id'=>'disembodiment-form', 'novalidate')) !!}
            <div id="disembodiment-option" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 70%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h3 class="modal-title">Disembodiment</h3>
                        </div>
                        <div class="modal-body">
                            <div class="register-box" style="width: auto;margin: 0">
                                <div class="register-box-body  margin-bottom">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                                            ng-show="isVerifying"><i
                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&!memorandumId">Memorandum ID is required.</span><span
                                                            class="text-danger"
                                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                                       type="text" class="form-control" name="memorandum_id"
                                                       placeholder="Enter memorandum id" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Date&nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                                                         ng-if="newDisembodimentForm.disembodiment_date.$touched && newDisembodimentForm.disembodiment_date.$error.required">Date is required.</span>
                                                </label>

                                                {!! Form::date('disembodiment_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'disembodiment_date',  'ng-model'=> 'disembodiment_date', 'required')) !!}

                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Comment
                                                    &nbsp;&nbsp;&nbsp;<span class="text-danger"
                                                                            ng-if="newDisembodimentForm.disembodiment_comment.$touched && newDisembodimentForm.disembodiment_comment.$error.required">Comment is required.</span></label>

                                                {!! Form::text('disembodiment_comment', $value = null, $attributes = array('class' => 'form-control', 'id' => 'disembodiment_comment', 'ng-model'=> 'disembodiment_comment', 'placeholder'=> 'Write Comment', 'required')) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">
                                            <tr class="info">
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Division</th>
                                                <th>Ansar Unit</th>
                                                <th>Ansar Thana</th>
                                                <th>Designation</th>
                                                <th>KPI Name</th>
                                                <th>Disembodiment Reason</th>
                                            </tr>

                                            <tbody id="ansar-all-modal">
                                            </tbody>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" id="disembodiment-entry" ng-disabled="newDisembodimentForm.disembodiment_date.$error.required||newDisembodimentForm.disembodiment_comment.$error.required||!memorandumId||isVerified||isVerifying">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
                                    </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        var selectedAnsars = [];
        var ansar_ids = [];
        $('#load-ansar-for-disembodiment').click(function () {
            $("#all-loading").css('display', 'block');
            var selectedKpi = $('select[name=kpi_id]').val();
            var selectedThana = $('select[name=thana_id]').val();

            $.ajax({
                url: '{{URL::route('EmbodimentController@loadAnsarForDisembodiment')}}',
                type: 'get',
                data: {kpi_id: selectedKpi, thana_id: selectedThana},
                success: function (data) {
                    //$('#ansar-all').html(data);
                    $("#all-loading").css('display', 'none');
                    if (data.result == undefined) {
                        $('#disembodiment-confirmation').prop('disabled', false);
                        $("#ansar-all").html(data);
                        //h = data;
                    }
                    else {
                        $('#disembodiment-confirmation').prop('disabled', true);
//                        alert($("#status-all").html())
                        $("#ansar-all").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">No Ansar Found to show</td> </tr>');
                    }
                }
            })
        });
        $('#disembodiment-confirmation').click(function () {
            var innerHtml = "";
            selectedAnsars.forEach(function (a, b, c) {
                var d = a.clone();
                var text = $(a.children('td')[7]).children('select').children('option:selected').html();
                var value = $(a.children('td')[7]).children('select').val();
                d.children('td')[0].innerHTML += "<input type='hidden' name='selected-ansar_id[]' value='" + $.trim($(d.children('td')[0]).text()) + "'>";
                ansar_ids.push($.trim($(d.children('td')[0]).text()));
                d.children('td')[7].innerHTML = text + "<input type='hidden' name='reason[]' value='" + value + "'>";
                d.children('td')[8].remove();
                innerHtml += '<tr>' + d.html() + '</tr>';
            })
            $('#ansar-all-modal').html(innerHtml)
        });

        $(document).on('change', '.ansar-check', function () {
            selectedAnsars = [];
            $('.ansar-check:checked').each(function () {
                selectedAnsars.push($(this).parents('tr'))
            })
//            if (this.checked) {
//                //alert($(this).parents('tr').splice(7, 1).html())
//                selectedAnsars.push($(this).parents('tr'))
//            } else {
////                alert("Hello");
//                selectedAnsars.splice(selectedAnsars.indexOf($(this).parents('tr')), 1)
//            }
//            alert(selectedAnsars.length)
        })

        $('#disembodiment-entry').click(function (e) {
                e.preventDefault();
                $("#disembodiment-form").ajaxSubmit({
                    success: function (a, b, c, d) {
                        console.log(a)
                        selectedAnsars.forEach(function (a, b, c) {
                            a.remove()
                        })
                        if (a.status) {
                            $('body').notifyDialog({type: 'success', message: a.message}).showDialog()
                        }
                    },
                    error: function (a, b, c, d) {
                        console.log(a)
                    },
                    beforeSubmit: function (arr) {
                       // arr.push({type:'text', value: selectedValue, name: 'come_from_where'})
                        console.log(arr)
                    }
                })
                $(".close").trigger('click');
            });
    </script>
@endsection
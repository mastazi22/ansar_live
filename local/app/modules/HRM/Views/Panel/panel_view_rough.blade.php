{{--User: Shreya--}}
{{--Date: 10/15/2015--}}
{{--Time: 10:49 AM--}}

@extends('template.master')
@section('title','Panel')
@section('small_title')
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#panel-modal"><span class="glyphicon glyphicon-save"></span> Load Ansars</button>
    @endsection
@section('breadcrumb')
    {!! Breadcrumbs::render('panel_information') !!}
    @endsection
@section('content')
    <script>
        GlobalApp.controller("PanelController", function ($scope, $window, $http, $timeout) {

            $scope.memorandumId = "";
            $scope.joinDate = "";
            $scope.isVerified = false;
            $scope.isVerifying = false;
//            $scope.modalOpen = false;
//            $scope.selectAll = false;
//            $scope.showDialog = false;

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

        })
        GlobalApp.directive('openHideModal', function () {
            return {
                restrict: 'AC',
                link: function (scope, elem, attr) {
                    $(elem).on('click', function () {
                        //alert("hh")
                        scope.memorandumId = "";
                        scope.panel_date = "";
                        scope.$digest()
                        $("#confirm-panel-modal").modal("toggle")
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

    <div ng-controller="PanelController">
        {{--<div class="breadcrumbplace">--}}
            {{--{!! Breadcrumbs::render('panel_information') !!}--}}
        {{--</div>--}}
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <meta name="csrf-token" content="{{ csrf_token() }}"/>
        <section class="content">

            <div class="box box-solid" style="min-height: 200px;">
                <div class="overlay" id="all-loading" style="display: none">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <div class="row" >
                        <div class="col-md-12">
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="pc-table">

                                    <tr>
                                        <th>Ansar ID</th>
                                        <th>Ansar Name</th>
                                        <th>Ansar Rank</th>
                                        <th>Ansar Unit</th>
                                        <th>Ansar Thana</th>
                                        <th>Date of Birth</th>
                                        <th>Sex</th>
                                        <th>Merit List</th>
                                        <th>
                                            <div class="styled-checkbox">
                                                <input type="checkbox" id="check-all-panel">
                                                <label for="check-all-panel"></label>
                                            </div>
                                        </th>
                                        {{--<th><input type="checkbox" id="select-all-panel" name="" value=""--}}
                                        {{--style="height: 20px; width: 25px"> Select All--}}
                                        {{--</th>--}}
                                    </tr>
                                    <tbody id="status-all" class="status">
                                    <tr colspan="11" class="warning" id="not-find-info">
                                        <td colspan="11">No Ansar Found to add to Panel</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-info btn-sm pull-right" id="confirm-panel" open-hide-modal disabled>Add to Panel
                    </button>
                </div>
            </div>
            <!-- /.box
            -footer -->
            <!--Modal Open-->
            <div id="panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="box-info modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title">Panel Options</h3>
                        </div>
                        <div class="modal-body">
                            <div class="offer-loading" ng-show="showLoadingScreen">
                                <i class="fa fa-spinner fa-pulse fa-2x" style="position: relative;left:48%;top:40%"></i>
                            </div>
                            <div class="box" style="border-top: none;">
                                <div class="box-body">
                                    <form role="form" id="load_ansar_for_panel" action="{{URL::route('select_status')}}" method="get">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label> Select a Status</label>
                                                            <select name='come_from_where' id='come_from_where'
                                                                    class="form-control">
                                                                <option value="" disabled selected>--Select a Status--</option>
                                                                <option value="1">Rest Status</option>
                                                                <option value="2">Free Status</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <ul style="list-style: none; margin-left: 0 !important;padding: 0"
                                                        class="row custom-selected">
                                                        <li class="form-group col-md-5 custom-selected" id="from-id">
                                                            <label>From (ID)</label>
                                                            <input type="text" name="from-id" class="form-control" placeholder="Ansar ID">
                                                        </li>
                                                        <li class="col-sm-1" style="text-align: center;font-size: 1.2em;padding: 0;width: auto;">
                                                            <label class="control-label" style="display: block">&nbsp;</label>
                                                            to
                                                        </li>
                                                        <li class="form-group col-md-5" id="to-id">
                                                            <label>To (ID)</label>
                                                            <input type="text" name="to-id" class="form-control" placeholder="Ansar ID">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Select no. of Ansars to Load</label>
                                                <select class="form-control" name="ansar_num" id="count-ansar">
                                                    <option value="">--Select--</option>
                                                    <option value="10">10</option>
                                                    <option value="20">20</option>
                                                    <option value="30">30</option>
                                                    <option value="40">40</option>
                                                    <option value="50">50</option>
                                                    <option value="60">60</option>
                                                    <option value="70">70</option>
                                                    <option value="80">80</option>
                                                    <option value="90">90</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info pull-right" id="load-panel">
                                            <i class="fa fa-download"></i> Load
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!--Modal Open-->
            <div id="confirm-panel-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="width: 80%;overflow: auto;">
                    <div class="box-info modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                    ng-click="modalOpen = false">&times;</button>
                            <h3 class="modal-title">Confirmation for Adding Ansars to Panel</h3>
                        </div>
                        {!! Form::open(array('route' => 'save-panel-entry', 'id'=>'panel-form', 'name' => 'panelForm', 'method' => 'post')) !!}
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
                                                       placeholder="Enter Memorandum no." required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">Panel Date <span class="text-danger"
                                                                                              ng-show="panelForm.panel_date.$touched && panelForm.panel_date.$error.required"> Date is required.</span></label>
                                                &nbsp;&nbsp;&nbsp;</label>
                                                {!! Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'panel_date', 'ng_model' => 'panel_date', 'required')) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="pc-table">
                                            <tr>
                                                <th>Ansar ID</th>
                                                <th>Ansar Name</th>
                                                <th>Ansar Rank</th>
                                                <th>Ansar Unit</th>
                                                <th>Ansar Thana</th>
                                                <th>Date of Birth</th>
                                                <th>Sex</th>
                                                <th>Merit List</th>
                                            </tr>
                                            <tbody id="status-all-modal" class="status">
                                            <tr colspan="9" class="warning" id="not-find-info">
                                                <td colspan="9">No Ansar Found to Withdraw</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button class="btn btn-primary pull-right" id="confirm-panel-entry" ng-disabled="!panel_date||!memorandumId||isVerified||isVerifying">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
                                    </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!--Modal Close-->
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        })
        var h = "";
        $("#load_ansar_for_panel").ajaxForm({
            beforeSubmit: function () {
                $("#all-loading").css('display', 'block');
                $('#check-all-panel').prop('checked', false);
                $('.check-panel').prop('checked', false);
                selectedAnsars = [];
                $("#panel-modal").modal('toggle')
            },
            success: function (data) {
                console.log()
                $("#status-all").html("");
                $("#confirm-panel").prop('disabled',true);
                if (data.result == undefined) {
                    //$('#confirm-panel').prop('disabled', false);
                    $("#status-all").html(data);
                    h = data;
                    $("#all-loading").css('display', 'none');
                }
                else {
                    $("#all-loading").css('display', 'none');
                    $('#confirm-panel').prop('disabled', true);
//                        alert($("#status-all").html())
                    $("#status-all").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">No Ansar Found to add to Panel</td> </tr>');
                }
                $("#load_ansar_for_panel")[0].reset();
            },
            error: function (response) {
                $("#status-all").html('<tr colspan="11" class="warning" id="not-find-info"> <td colspan="11">'+response.responseText+'</td> </tr>');
                console.log(response);
            }
        })
        $('#confirm-panel').click(function (e) {
            e.preventDefault();
            var innerHtml = "";
            selectedAnsars.forEach(function (a, b, c) {
                var d = a.clone();
                var text = $(a.children('td')[7]).children('input').val();
                d.children('td')[0].innerHTML += "<input type='hidden' name='selected-ansar_id[]' value='" + $.trim($(d.children('td')[0]).text()) + "'>";
                // ansar_ids.push($.trim($(d.children('td')[0]).text()));
                d.children('td')[7].innerHTML = text + "<input type='hidden' name='ansar_merit[]' value='" + text + "'>";
                d.children('td')[8].remove();
                innerHtml += '<tr>' + d.html() + '</tr>';
            })
            $('#status-all-modal').html(innerHtml)
        });


        var selectedAnsars = [];
        $(document).on('change', '.check-panel', function () {
            if ($('#check-all-panel').prop("checked") == true) {
                $('#check-all-panel').prop('checked', $(this).prop('checked'));
            }
            if ($('.check-panel:checked').length == ($('.check-panel').length)) {
                $('#check-all-panel').prop('checked', 'checked');
            }
            selectedAnsars = []
            $('.check-panel:checked').each(function () {
                selectedAnsars.push($(this).parents('tr'))
            })
            if(selectedAnsars.length==0){
                $("#confirm-panel").prop('disabled',true);
            }
            else{
                $("#confirm-panel").prop('disabled',false);
            }
        })

        $('#confirm-panel-entry').click(function (e) {
            $("#all-loading").css('display', 'block');
            e.preventDefault();
            $("#panel-form").ajaxSubmit({
                success: function (a, b, c, d) {
                    console.log(a)
                    selectedAnsars.forEach(function (a, b, c) {
                        a.remove()
                    })
                    if (a.status) {
                        $("#all-loading").css('display', 'none');
                        $('body').notifyDialog({type: 'success', message: a.message}).showDialog()

                    }
                },

                error: function (a, b, c, d) {
                    console.log(a)
                    $("#all-loading").css('display', 'none');
                    document.write(a.responseText)
                    $('body').notifyDialog({type: 'error', message: "Server Error. Please Try again!"}).showDialog()

                },
                beforeSubmit: function (arr) {
//                    $("#all-loading").css('display', 'block');
                    arr.push({type: 'text', value: selectedValue, name: 'come_from_where'})
                    console.log(arr)

                }
            })
            $(".close").trigger('click');
            $('#show-ansar').css('display', 'block');
        });

        $("#check-all-panel").change(function () {
            $(".check-panel").prop('checked', $(this).prop('checked'));
            $(".check-panel").trigger('change')
        });
        /****************************************************/
        $('#panel_date').datePicker(true);
    </script>
@endsection